<?php
App::uses('AppModel', 'Model');
App::uses('File', 'Utility');

class MysqlBackup extends UtilCakeAppModel {

	public $useTable = false;
	public $table = false;
	public $actAs = false;

	/**
	 * Dumps the MySQL database that this controller's model is attached to.
	 * This action will serve the sql file as a download so that the user can save the backup to their local computer.
	 *
	 * @param string $tables Comma separated list of tables you want to download, or '*' if you want to download them all.
	 */
	public function generate($tables = '*') {

		$return = '';
		$dataSource = $this->getDataSource();
		$databaseName = $dataSource->getSchemaName();


		// Do a short header
		$return .= '-- Database: `' . $databaseName . '`' . "\n";
		$return .= '-- Generation time: ' . date('D jS M Y H:i:s') . "\n\n\n";

		$return .= '-- Desactivar la comprobación de la integridad referencial' . "\n";
		$return .= 'SET FOREIGN_KEY_CHECKS = 0;' . "\n\n\n";


		if ($tables == '*') {
			$tables = array();
			$result = $this->query('SHOW TABLES');
			foreach($result as $resultKey => $resultValue){
				$tables[] = current($resultValue['TABLE_NAMES']);
			}
		} else {
			$tables = is_array($tables) ? $tables : explode(',', $tables);
		}

		// Run through all the tables
		foreach ($tables as $table) {
			$tableData = $this->query('SELECT * FROM ' . $table);

			$return .= '-- Table:  `' . $table . '`' . "\n\n";
			$return .= 'DROP TABLE IF EXISTS ' . $table . ';';

			$createTableResult = $this->query('SHOW CREATE TABLE ' . $table);
			$createTableEntry = current(current($createTableResult));
			$return .= "\n\n" . $createTableEntry['Create Table'] . ";\n\n";

			// Output the table data
			foreach($tableData as $tableDataIndex => $tableDataDetails) {
				$return .= 'INSERT INTO '. $table .' VALUES(';
				foreach($tableDataDetails[$table] as $dataKey => $dataValue) {
					if(is_null($dataValue)){
						$escapedDataValue = 'NULL';
					}else{
						// Convert the encoding
						//$escapedDataValue = mb_convert_encoding( $dataValue, "UTF-8", "ISO-8859-1" );
						$escapedDataValue = mb_convert_encoding( $dataValue, "UTF-8" );

						// Escape any apostrophes using the datasource of the model.
						$escapedDataValue = $this->getDataSource()->value($escapedDataValue);
					}
					$tableDataDetails[$table][$dataKey] = $escapedDataValue;
				}
				$return .= implode(',', $tableDataDetails[$table]);
				$return .= ");\n";
			}
			$return .= "\n\n\n";
		}
		$return .= '-- Activar la comprobación de la integridad referencial' . "\n";
		$return .= 'SET FOREIGN_KEY_CHECKS = 1;' . "\n\n\n";
		return $return;
	}

	public function generateToFile($destino, $tables = '*'){
		$file = new File($destino, true);
		$info = $file->info();
		if(mb_strtolower($info['extension']) === 'sql' ){
			$file->write( $this->generate($tables) );
			$file->close();
			return true;
		}
		return false;
	}

	public function restore($query){
		if( $this->query($query)){
			return true;
		}
		return false;
	}

	public function restoreToFile($fuente){
		$file = new File($fuente, false);
		$info = $file->info();
		if(mb_strtolower($info['extension']) === 'sql' ){
			$query = $file->read();
			$result = $this->query($query);
			//debug($result);

			//$log = $this->getDataSource()->getLog(false, false);
			//unset($log['log'][0]['query']);
			//debug($log);

			//exit();
			//$file->close();
			if($result){
				return true;
			}
		}
		return false;
	}



	public function dbName(){
		$dataSource = $this->getDataSource();
		return $dataSource->getSchemaName();
	}

	public function generateName($uno = null, $dos = null, $date = null){
		$uno = ( $uno == null ? $this->dbName() : $uno );
		$dos = ( $dos == null ? 'backup' : $dos );
		$date = ( $date == null ? date('YmdHis') : $date );
		return $uno.'_'.$dos.'_'.$date.'.sql';
	}


	/*
	// Use MysqlBackup from a Controller
	// app/Controller/AnyController.php
	public function mysql_backup($tables = '*'){
		$this->loadModel('UtilCake.MysqlBackup');
		$return = $this->MysqlBackup->generate($tables);
		$fileName = $this->MysqlBackup->generateName();

		$this->autoRender = false;
		$this->response->type('text/x-sql');
		$this->response->charset('utf8');
		$this->response->body($return);
		$this->response->download($fileName);

		return $this->response;
	}
	*/





}
