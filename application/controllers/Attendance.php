<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Attendance_model');
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    // Halaman Absen Harian (Check In/Out)
    public function log()
    {
        $user_id = $this->session->userdata('user_id');
        $employee = $this->db->get_where('employees', ['user_id' => $user_id])->row();
        $settings = $this->db->get('settings')->row();
        
        // Cari status absen yang masih menggantung (belum check out)
        $data['today_log'] = $this->db->order_by('id', 'DESC')->get_where('attendance', [
            'employee_id' => $employee->id,
            'clock_out' => NULL
        ])->row();

        // Jika tidak ada yang menggantung, ambil log absensi hari ini
        if (!$data['today_log']) {
            $data['today_log'] = $this->db->order_by('id', 'DESC')->get_where('attendance', [
                'employee_id' => $employee->id,
                'date' => date('Y-m-d')
            ])->row();
        }

        if ($data['today_log'] && $data['today_log']->shift_id) {
            $data['today_shift'] = $this->db->select('id as shift_id, name as shift_name, start_time, end_time, color')
                                         ->get_where('master_shifts', ['id' => $data['today_log']->shift_id])->row();
        } else {
            $data['today_shift'] = $this->Attendance_model->get_today_shift($employee->id);
        }

        $data['settings'] = $settings;

        $this->load->view('layout/header');
        $this->load->view('attendance/log', $data);
        $this->load->view('layout/footer');
    }

    // Proses Check In / Check Out via Ajax
    public function punch()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

        $user_id = $this->session->userdata('user_id');
        $employee = $this->db->get_where('employees', ['user_id' => $user_id])->row();

        if(!$employee) {
            echo json_encode(['status' => 'error', 'message' => 'Profil pegawai tidak ditemukan']);
            return;
        }

        $type = $this->input->post('type'); // 'in' or 'out'
        $lat  = $this->input->post('lat');
        $long = $this->input->post('long');
        $image = $this->input->post('image'); // Base64 selfie

        if(empty($lat) || empty($long)) {
            echo json_encode(['status' => 'error', 'message' => 'GPS dibutuhkan untuk melakukan absensi.']);
            return;
        }

        // 1. Check Geofencing
        $settings = $this->db->get('settings')->row();
        $distance = $this->Attendance_model->get_distance($lat, $long, $settings->latitude, $settings->longitude);

        if($distance > $settings->radius_meters) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Anda berada di luar jangkauan area Rumah Sakit. Jarak Anda: ' . round($distance) . ' meter.'
            ]);
            return;
        }

        // 2. Handle Thumbnail / Selfie
        $img_path = NULL;
        if($image) {
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $image_data = base64_decode($image);
            $filename = 'attendance_' . $type . '_' . time() . '.jpg';
            $path = './uploads/attendance/';
            if (!is_dir($path)) mkdir($path, 0777, TRUE);
            file_put_contents($path . $filename, $image_data);
            $img_path = 'uploads/attendance/' . $filename;
        }

        $today = date('Y-m-d');
        $now = date('H:i:s');
        $role = $this->session->userdata('role');

        if($type == 'in') {
            // Get most suitable shift for right now
            $shift = $this->Attendance_model->get_today_shift($employee->id, $now);
            
            if(!$shift) {
                echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki jadwal tugas saat ini.']);
                return;
            }

            // Check if already checked in for THIS specific shift today
            $existing = $this->db->get_where('attendance', [
                'employee_id' => $employee->id,
                'date' => $today,
                'shift_id' => $shift->shift_id
            ])->row();

            if($existing) {
                echo json_encode(['status' => 'error', 'message' => 'Anda sudah Check In untuk shift '.$shift->shift_name.' hari ini!']);
                return;
            }

            $status = (strtotime($now) > strtotime($shift->start_time)) ? 'late' : 'present';

            $data = [
                'employee_id' => $employee->id,
                'shift_id' => $shift->shift_id,
                'date' => $today,
                'clock_in' => $now,
                'status' => $status,
                'photo_in' => $img_path,
                'location_lat_in' => $lat,
                'location_long_in' => $long,
                'distance_meters_in' => round($distance),
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('attendance', $data);
            echo json_encode(['status' => 'success', 'message' => 'Berhasil Check In!', 'time' => $now]);

        } elseif($type == 'out') {
             // Find the log that hasn't checked out yet, regardless of today's date (untuk night shift)
             $log = $this->db->order_by('id', 'DESC')->get_where('attendance', [
                 'employee_id' => $employee->id,
                 'clock_out' => NULL
             ])->row();

             if(!$log) {
                 echo json_encode(['status' => 'error', 'message' => 'Anda tidak memiliki antrean Check Out. Silakan Check In dulu.']);
                 return;
             }

             // Enforce early check-out block
             $shift = $this->db->get_where('master_shifts', ['id' => $log->shift_id])->row();
             if($shift) {
                $start_ts = strtotime($log->date . ' ' . $shift->start_time);
                $end_ts = strtotime($log->date . ' ' . $shift->end_time);
                
                // Jika shift melewati tengah malam, tambahkan 1 hari (86400 detik)
                if($end_ts < $start_ts) {
                    $end_ts += 86400;
                }

                $now_ts = strtotime($today . ' ' . $now);
                
                // Enforce early check-out block (Strict for everyone)
                if($now_ts < $end_ts) {
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Belum waktunya jam pulang. Jam pulang ' . $shift->shift_name . ' adalah ' . substr($shift->end_time, 0, 5) . '.'
                    ]);
                    return;
                }
             }

             $update = [
                 'clock_out' => $now,
                 'photo_out' => $img_path,
                 'location_lat_out' => $lat,
                 'location_long_out' => $long,
                 'distance_meters_out' => round($distance)
             ];
             $this->db->where('id', $log->id)->update('attendance', $update);
             echo json_encode(['status' => 'success', 'message' => 'Berhasil Check Out!', 'time' => $now]);
        }
    }

    // Halaman Riwayat Absensi
    public function history()
    {
        // Using master_units as the source for departments/units
        $data['departments'] = $this->db->get('master_units')->result();

        $this->load->view('layout/header');
        $this->load->view('attendance/history', $data);
        $this->load->view('layout/footer');
    }

    public function get_history_json()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $department_id = $this->input->get('department_id');

        $this->db->select('attendance.*, employees.full_name, employees.nip, ms.name as shift_name, ms.start_time, ms.end_time');
        $this->db->from('attendance');
        $this->db->join('employees', 'attendance.employee_id = employees.id');
        $this->db->join('master_shifts ms', 'attendance.shift_id = ms.id', 'left');
        
        if($start_date) $this->db->where('date >=', $start_date);
        if($end_date) $this->db->where('date <=', $end_date);
        if($department_id) $this->db->where('employees.unit_id', $department_id);

        $this->db->order_by('date', 'DESC');
        $rows = $this->db->get()->result();

        $data = [];
        $no = 1;

        foreach($rows as $row) {
            $late_label = '-';
            $overtime_label = '-';

            if ($row->clock_in && $row->start_time) {
                $clock_in_ts = strtotime($row->date . ' ' . $row->clock_in);
                $start_ts = strtotime($row->date . ' ' . $row->start_time);
                if ($clock_in_ts > $start_ts) {
                    $diff = $clock_in_ts - $start_ts;
                    $hours = floor($diff / 3600);
                    $mins = floor(($diff % 3600) / 60);
                    $late_label = ($hours > 0 ? "{$hours}h " : "") . "{$mins}m";
                }
            }

            if ($row->clock_out && $row->end_time) {
                $clock_out_ts = strtotime($row->date . ' ' . $row->clock_out);
                $end_ts = strtotime($row->date . ' ' . $row->end_time);
                
                // Handle shift crossing midnight
                if ($end_ts < strtotime($row->date . ' ' . ($row->start_time ?? '00:00:00'))) {
                    $end_ts += 86400;
                }

                if ($clock_out_ts > $end_ts) {
                    $diff = $clock_out_ts - $end_ts;
                    $hours = floor($diff / 3600);
                    $mins = floor(($diff % 3600) / 60);
                    $overtime_label = ($hours > 0 ? "{$hours}h " : "") . "{$mins}m";
                }
            }

            // Badge Status
            if($row->status == 'late') {
                $status = '<span class="px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700 text-[10px] font-black uppercase tracking-widest border border-yellow-200">Terlambat</span>';
            } elseif($row->status == 'present') {
                $status = '<span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-black uppercase tracking-widest border border-green-200">Hadir</span>';
            } else {
                $status = '<span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-widest border border-red-200">'.($row->status ?: 'Alpha').'</span>';
            }

            $data[] = [
                $no++,
                '<div class="flex items-center gap-3">
                    <div class="size-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-black text-[10px]">'.substr($row->full_name, 0, 2).'</div>
                    <div class="flex flex-col">
                        <span class="font-black text-gray-900 text-sm">'.$row->full_name.'</span>
                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">'.($row->shift_name ?? 'Tanpa Shift').'</span>
                    </div>
                </div>',
                '<span class="text-sm font-bold text-gray-600">'.date('d M Y', strtotime($row->date)).'</span>',
                '<span class="font-mono font-bold text-gray-900">'.($row->clock_in ? date('H:i', strtotime($row->clock_in)) : '-').'</span>',
                '<span class="font-mono font-bold text-gray-900">'.($row->clock_out ? date('H:i', strtotime($row->clock_out)) : '-').'</span>',
                '<span class="text-xs font-black text-amber-600">'.$late_label.'</span>',
                '<span class="text-xs font-black text-blue-600">'.$overtime_label.'</span>',
                $status
            ];
        }
        echo json_encode(['data' => $data]);
    }

    public function export_pdf()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $department_id = $this->input->get('department_id');

        $this->db->select('attendance.*, employees.full_name, employees.nip, ms.name as shift_name, ms.start_time, ms.end_time');
        $this->db->from('attendance');
        $this->db->join('employees', 'attendance.employee_id = employees.id');
        $this->db->join('master_shifts ms', 'attendance.shift_id = ms.id', 'left');
        
        if($start_date) $this->db->where('date >=', $start_date);
        if($end_date) $this->db->where('date <=', $end_date);
        if($department_id) $this->db->where('employees.unit_id', $department_id);

        $this->db->order_by('date', 'DESC');
        $data['attendance'] = $this->db->get()->result();
        $data['settings'] = $this->db->get('settings')->row();
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        
        $data['department_name'] = 'Semua Departemen';
        if($department_id) {
            $dept = $this->db->get_where('master_units', ['id' => $department_id])->row();
            if($dept) $data['department_name'] = $dept->name;
        }

        $html = $this->load->view('attendance/pdf_report', $data, TRUE);
        
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L', // Landscape
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('Laporan_Kehadiran_' . date('Ymd') . '.pdf', 'I');
    }
}
