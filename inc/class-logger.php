<?php

namespace HM\Cavalcade\Runner;

class Logger {

	public function __construct( $db, $table_prefix ) {
		$this->db = $db;
		$this->table_prefix = $table_prefix;
	}

	public function log_job_completed( Job $job, $message = '' ) {
        $runtime = time() - $job->startTimestamp;

        $content = "Message: $message" . "<br/>";
        $content .= "Runtime: $runtime s" . "<br/>";
        $content .= "Hook: " . $job->hook . "<br/>";
        $content .= "Args: " . json_encode($job->args);

		$this->log_run( $job->id, 'completed', $content );
	}

	public function log_job_failed( Job $job, $message = '' ) {
        $runtime = time() - $job->startTimestamp;

        $content = "Message: $message" . "<br/>";
        $content .= "Runtime: $runtime s" . "<br/>";
        $content .= "Hook: " . $job->hook . "<br/>";
        $content .= "Args: " . json_encode($job->args);

		$this->log_run( $job->id, 'failed', $content );
	}

	protected function log_run( $job_id, $status, $message = '' ) {
		$query = "INSERT INTO {$this->table_prefix}cavalcade_logs (`job`, `status`, `timestamp`, `content`)";
		$query .= ' values( :job, :status, :timestamp, :content )';

		$statement = $this->db->prepare( $query );
		$statement->bindValue( ':job', $job_id );
		$statement->bindValue( ':status', $status );
		$statement->bindValue( ':timestamp', date( MYSQL_DATE_FORMAT ) );
		$statement->bindValue( ':content', $message );
		$statement->execute();
	}
}
