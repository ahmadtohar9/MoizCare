<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_model extends CI_Model {

    public function get_distance($lat1, $lon1, $lat2, $lon2) {
        $earth_radius = 6371000; // in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earth_radius * $c;
        return $distance;
    }

    public function get_today_shift($employee_id, $specific_time = null) {
        $today = date('Y-m-d');
        $now_str = $specific_time ?: date('H:i:s');
        $now_ts = strtotime($today . ' ' . $now_str);

        // Fetch all approved shifts for this employee today
        $this->db->select('sd.shift_id, ms.name as shift_name, ms.start_time, ms.end_time, ms.color');
        $this->db->from('schedule_submission_details sd');
        $this->db->join('schedule_submissions ss', 'sd.submission_id = ss.id');
        $this->db->join('master_shifts ms', 'sd.shift_id = ms.id');
        $this->db->where('ss.employee_id', $employee_id);
        $this->db->where('ss.status', 'approved');
        $this->db->where('sd.date', $today);
        $shifts = $this->db->get()->result();

        if (empty($shifts)) return null;

        // Try to find the shift where NOW is within the window (start-2h to end)
        foreach ($shifts as $s) {
            $start_ts = strtotime($today . ' ' . $s->start_time);
            $end_ts = strtotime($today . ' ' . $s->end_time);
            
            // Adjust for midnight crossing
            if ($end_ts < $start_ts) $end_ts += 86400;

            if ($now_ts >= ($start_ts - 7200) && $now_ts <= $end_ts) {
                return $s;
            }
        }
        
        // Fallback: Pick the closest starting shift
        $closest = null;
        $min_diff = null;
        foreach ($shifts as $s) {
            $start_ts = strtotime($today . ' ' . $s->start_time);
            $diff = abs($now_ts - $start_ts);
            if ($min_diff === null || $diff < $min_diff) {
                $min_diff = $diff;
                $closest = $s;
            }
        }
        return $closest;
    }
}
