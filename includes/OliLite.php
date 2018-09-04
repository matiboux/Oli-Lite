<?php
/*\
|*|  ---------------------------------
|*|  --- [       Oli Lite       ] ---
|*|  --- [  Version BETA 1.0.0  ] ---
|*|  ---------------------------------
|*|  
|*|  Oli Lite is a PHP framework with MySQL integration and URL management tools
|*|  Oli Lite is an open-source project! Check it out on Github:
|*|    https://github.com/matiboux/Oli-Lite/
|*|  
|*|  Oli Lite is a simplified version of Oli, check it out on Github:
|*|    https://github.com/matiboux/Oli/
|*|  
|*|  Creator & Developer: Matiboux (Mathieu Guérin)
|*|   → Github: @matiboux – https://github.com/matiboux/
|*|   → Twitter: @Matiboux – https://twitter.com/Matiboux/
|*|   → Telegram: @Matiboux – https://t.me/Matiboux/
|*|   → Email: matiboux@gmail.com
|*|  
|*|  For more info, please read the README.md file.
|*|  You can find it in the root directory or the project repository:
|*|    https://github.com/matiboux/Oli-Lite/
|*|  
|*|  --- --- ---
|*|  
|*|  // MIT License
|*|  Copyright (C) 2018 Matiboux (Mathieu Guérin)
|*|  You'll find a copy of the MIT license in the LICENSE file.
|*|  
|*|  --- --- ---
|*|  
|*|  Project created on August 7, 2018.
|*|  Releases:
|*|   * BETA 1.0.0: [WIP]
\*/

/*\
|*|  ╒════════════════════════╕
|*|  │ :: TABLE OF CONTENT :: │
|*|  ╞════════════════════════╛
|*|  │
|*|  ├ I. Variables
|*|  ├ II. Magic Methods
|*|  ├ III. Oli
|*|  │ ├ 1. Oli Infos
|*|  │ └ 2. Tools
|*|  │
|*|  ├ IV. Configuration
|*|  │ ├ 1. App Config
|*|  │ ├ 3. MySQL
|*|  │ ├ 4. General
|*|  │
|*|  ├ V. MySQL
|*|  │ ├ 1. Status
|*|  │ ├ 2. Read
|*|  │ ├ 3. Write
|*|  │ └ 4. Database Edits
|*|  │   ├ A. Tables
|*|  │   └ B. Columns
|*|  │
|*|  └ VI. General
|*|    ├ 1. Load Website
|*|    ├ 2. Settings
|*|    ├ 3. Custom Content
|*|    ├ 4. HTTP Tools
|*|    │ ├ A. Content Type
|*|    │ ├ B. Cookie Management
|*|    │ │ ├ a. Read Functions
|*|    │ │ └ b. Write Functions
|*|    │ └ C. Mail Management
|*|    ├ 5. HTML Tools
|*|    │ ├ A. File Loaders
|*|    │ └ B. File Minimizers
|*|    ├ 6. Url Functions
|*|    └ 7. Utility Tools
|*|      ├ A. Generators
|*|      │ ├ a. UUID
|*|      │ └ b. Misc
|*|      ├ B. Data Conversion
|*|      ├ C. Date & Time
|*|      └ D. Client Infos
\*/

namespace Oli {

class OliLite {

	/** -------------- */
	/**  I. Variables  */
	/** -------------- */
	
	/** Read-only variables */
	private $readOnlyVars = [
		'initTimestamp', 'oliInfos',
		'appConfig', 'config',
		'db', 'dbError',
		'fileNameParam', 'contentStatus'];
	
	/** Oli Vars */
	private $initTimestamp = null; // (PUBLIC READONLY)
	private $oliInfos = null; // (PUBLIC READONLY)
	
	/** Website Config */
	private $appConfig = null; // (PUBLIC READONLY)
	private $config = []; // Config (PUBLIC READONLY)
	
	/** Database Management */
	private $db = null; // MySQL PDO Object (PUBLIC READONLY)
	private $dbError = null; // MySQL PDO Error (PUBLIC READONLY)
	
	/** Content Management */
	private $fileNameParam = null; // Define Url Param #0 (PUBLIC READONLY)
	private $contentStatus = null; // Content Status (found, ...) (PUBLIC READONLY)
	
	/** Page Settings */
	private $contentType = null;
	private $charset = null;
	private $contentTypeForced = false;
	private $htmlLoaderList = [];
	
	
	/** *** *** */
	
	/** ------------------- */
	/**  II. Magic Methods  */
	/** ------------------- */
	
	/**
	 * OliLite Class Contruct function
	 * 
	 * @version BETA-1.0.0
	 * @updated BETA-1.0.0
	 * @return void
	 */
	public function __construct($initTimestamp = null) {
		/** Primary constants - Should have been defined in /load.php */
		if(!defined('ABSPATH')) die('Oli Error: ABSPATH is not defined.');
		if(!defined('INCLUDESPATH')) define('INCLUDESPATH', __DIR__ . '/');
		if(!defined('CONTENTPATH')) define('INCLUDESPATH', ABSPATH . 'content/');
		
		/** User Content constants */
		if(!defined('ASSETSPATH')) define('ASSETSPATH', CONTENTPATH . 'assets/');
		if(!defined('SCRIPTSPATH')) define('SCRIPTSPATH', CONTENTPATH . 'script/');
		if(!defined('THEMEPATH')) define('THEMEPATH', CONTENTPATH . 'theme/');
		
		/** Framework Init */
		$this->initTimestamp = $initTimestamp ?: microtime(true);
		$this->setContentType('DEFAULT', 'utf-8');
	}
	
	/**
	 * OliLite Class Destruct function
	 * 
	 * @version BETA-1.0.0
	 * @updated BETA-1.0.0
	 * @return void
	 */
	public function __destruct() {
		$this->loadEndHtmlFiles();
	}
	
	/**
	 * OliLite Class Read-only variables management
	 * 
	 * @version BETA-1.0.0
	 * @updated BETA-1.0.0
	 * @return mixed Returns the requested variable value if is allowed to read, null otherwise.
	 */
	public function __get($whatVar) {
		if(in_array($whatVar, $this->readOnlyVars)) {
			if($whatVar == 'oliInfos') return $this->getOliInfos();
			else return $this->$whatVar;
		} else return null;
    }
	
	/**
	 * OliLite Class Is Set variables management
	 * This fix the empty() false negative issue on inaccessible variables.
	 * 
	 * @version BETA-1.0.0
	 * @updated BETA-1.0.0
	 * @return mixed Returns true if the requested variable isn't empty and if is allowed to read, null otherwise.
	 */
    public function __isset($whatVar) {
        if(in_array($whatVar, $this->readOnlyVars)) return empty($this->$whatVar) === false;
        else return null;
    }
	
	/**
	 * OliLite Class to String function
	 * 
	 * @version BETA-1.0.0
	 * @updated BETA-1.0.0
	 * @return string Returns a short description of Oli.
	 */
	public function __toString() {
		return 'Powered by ' . $this->getOliInfos('name') . ', ' . $this->getOliInfos('short_description') . ' (v. ' . $this->getOliInfos('version') . ')';
	}
	
	/** *** *** */
	
	/** ---------- */
	/**  III. Oli  */
	/** ---------- */
	
		/** ------------------- */
		/**  III. 1. Oli Infos  */
		/** ------------------- */
		
		/**
		 * Get Oli Infos
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return string Returns a short description of Oli.
		 * @return string Returns Oli Infos.
		 */
		public function getOliInfos($whatInfo = null) {
			if(empty($this->oliInfos)) $this->oliInfos = file_exists(INCLUDESPATH . 'oli-infos.json') ? json_decode(file_get_contents(INCLUDESPATH . 'oli-infos.json'), true) : null; // Load Oli Infos if not already
			return !empty($whatInfo) ? $this->oliInfos[$whatInfo] : $this->oliInfos;
		}
		
		/** Get Team Infos */
		public function getTeamInfos($who = null, $whatInfo = null) {
			if(!empty($who)) {
				foreach($this->oliInfos['team'] as $eachMember) {
					if($eachMember['name'] == $who OR in_array($who, !is_array($eachMember['nicknames']) ? [$eachMember['nicknames']] : $eachMember['nicknames'])) {
						if(!empty($whatInfo)) return $eachMember[$whatInfo];
						else return $eachMember;
					}
				}
			} else return $this->oliInfos['team'];
		}
		
		/** -------------- */
		/**  III. 2. Tools  */
		/** -------------- */
		
		/** Get Execution Time */
		public function getExecutionTime($fromRequest = false) {
			if($fromRequest) return microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
			else return microtime(true) - $this->config['init_timestamp'];
		}
		public function getExecutionDelay($fromRequest = false) { return $this->getExecutionTime($fromRequest); }
		public function getExecuteDelay($fromRequest = false) { return $this->getExecutionTime($fromRequest); }
	
	/** *** *** */
	
	/** ------------------- */
	/**  IV. Configuration  */
	/** ------------------- */
		
		/** ------------------- */
		/**  IV. 1. App Config  */
		/** ------------------- */
		
		/**
		 * Get App Config
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return mixed Array or requested value.
		 */
		public function getAppConfig($index = null, $reload = false) {
			if(is_bool($index)) $reload = $index;
			
			if(($reload OR !isset($this->appConfig)) AND file_exists(ABSPATH . 'app.json')) $this->appConfig = json_decode(file_get_contents(ABSPATH . 'app.json'), true);
			
			if($this->appConfig !== null) {
				if(!empty($index)) return $this->appConfig[$index] ?: null;
				else return $this->appConfig ?: [];
			} else return null;
		}
		
		/** -------------- */
		/**  IV. 3. MySQL  */
		/** -------------- */
		
		/**
		 * MySQL Connection Setup
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return boolean Returns true if succeeded.
		 */
		public function setupMySQL($database, $username = null, $password = null, $hostname = null, $charset = null) {
			if($this->config['allow_mysql'] AND !empty($database)) {
				try {
					$this->db = new \PDO('mysql:dbname=' . $database . ';host=' . ($hostname ?: 'localhost') . ';charset=' . ($charset ?: 'utf8'), $username ?: 'root', $password ?: '');
				} catch(\PDOException $e) {
					$this->dbError = $e->getMessage();
					return false;
				}
			} else return false;
		}
		
		/**
		 * MySQL Connection Reset
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return boolean Returns true if succeeded.
		 */
		public function resetMySQL() {
			$this->db = null;
			$this->dbError = null;
		}
	
		/** ---------------- */
		/**  IV. 4. General  */
		/** ---------------- */
	
		/** Set Settings Tables */
		public function setSettingsTables($tables) {
			$this->config['settings_tables'] = $tables = !is_array($tables) ? [$tables] : $tables;
			$hasArray = false;
			foreach($tables as $eachTableGroup) {
				if(is_array($eachTableGroup) OR $hasArray) {
					$hasArray = true;
					$this->config['settings_tables'] = $eachTableGroup;
					$this->getUrlParam('base', $hasUsedHttpHostBase);
					
					if(!$hasUsedHttpHostBase) break;
				}
			}
			
			// $i = 1;
			// while($i <= strlen($this->config['media_path']) AND $i <= strlen($this->config['theme_path']) AND substr($this->config['media_path'], 0, $i) == substr($this->config['theme_path'], 0, $i)) {
				// $contentPath = substr($this->config['media_path'], 0, $i);
				// $i++;
			// }
			// define('CONTENTPATH', ABSPATH . ($contentPath ?: 'content/'));
			// define('MEDIAPATH', $this->config['media_path'] ? ABSPATH . $this->config['media_path'] : CONTENTPATH . 'media/');
			// define('THEMEPATH', $this->config['theme_path'] ? ABSPATH . $this->config['theme_path'] : CONTENTPATH . 'theme/');
		}
		
		/** Set CDN Url */
		public function setCdnUrl($url) {
			if(!empty($url)) {
				$this->config['cdn_url'] = $url;
			}
		}
	
	/** *** *** */
	
	/** ---------- */
	/**  V. MySQL  */
	/** ---------- */
	
		/** -------------- */
		/**  V. 1. Status  */
		/** -------------- */
		
		/**
		 * Is setup MySQL connection
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return boolean Returns the MySQL connection status
		 */
		public function isSetupMySQL() {
			return isset($this->db);
		}
	
		/** ------------ */
		/**  V. 2. Read  */
		/** ------------ */
		
		/**
		 * Run a raw MySQL Query
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return array|boolean Returns the query result content or true if succeeded.
		 */
		public function runQueryMySQL($query, $fetchStyle = true) {
			if(!$this->isSetupMySQL()) return null;
			else {
				$query = $this->db->prepare($query);
				if($query->execute()) return $query->fetchAll(!is_bool($fetchStyle) ? $fetchStyle : ($fetchStyle ? \PDO::FETCH_ASSOC : null));
				else {
					$this->dbError = $query->errorInfo();
					return false;
				}
			}
		}
		
		/**
		 * Get data from MySQL
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return array|boolean|void Returns data from the requested table if succeeded.
		 */
		public function getDataMySQL($table, ...$params) {
			if(!$this->isSetupMySQL()) return null;
			else {
				/** Select rows */
				if(!empty($params[0])) {
					if(is_array($params[0]) AND preg_grep("/^\S+$/", $params[0]) == $params[0]) $select = implode(', ', array_shift($params));
					else if(strpos($params[0], ' ') === false) $select = array_shift($params);
				}
				if(empty($select)) $select = '*';
				
				/** Fetch Style */
				if(!empty($params[count($params) - 1]) AND is_integer($params[count($params) - 1])) $fetchStyle = implode(', ', array_pop($params));
				else $fetchStyle = true;
				
				/** Custom parameters */
				$queryParams = null;
				if(!empty($params)) {
					foreach($params as $eachKey => $eachParam) {
						if(!empty($eachParam)) $queryParams .= ' ' . $eachParam;
					}
				}
				
				return $this->runQueryMySQL('SELECT ' . $select . ' FROM ' . $table . $queryParams, $fetchStyle);
			}
		}
		
		/**
		 * Get first info from table
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return array|null Returns first info from specified table.
		 */
		public function getFirstInfoMySQL($table, $whatVar = null, $sortBy = null, $rawResult = false) {
			if(!$this->isSetupMySQL()) return null;
			else {
				$dataMySQL = $this->getDataMySQL($table, $whatVar, !empty($sortBy) ? 'ORDER BY  `' . $sortBy . '` ASC' : null, 'LIMIT 1')[0];
				if(!empty($dataMySQL)) {
					if(!$rawResult) $where = array_map(function($value) {
						return (!is_array($value) AND is_array($decodedValue = json_decode($value, true))) ? $decodedValue : $value;
					}, $dataMySQL);
					return $dataMySQL;
				} else return null;
			}
		}
		/**
		 * Get first line from table
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @related OliLite::getFirstInfoMySQL()
		 * @return array|null Returns first line from specified table.
		 */
		public function getFirstLineMySQL($table, $sortBy = null, $rawResult = false) {
			return $this->getFirstInfoMySQL($table, null, $sortBy, $rawResult);
		}
		
		/**
		 * Get first info from table
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return array|null Returns last info from specified table.
		 */
		public function getLastInfoMySQL($table, $whatVar, $rawResult = false) {
			if(!$this->isSetupMySQL()) return null;
			else {
				$dataMySQL = array_reverse($this->getDataMySQL($table, $whatVar, !empty($sortBy) ? 'ORDER BY  `' . $sortBy . '` DESC' : null, !empty($sortBy) ? 'LIMIT 1' : null))[0];
				if(!empty($dataMySQL)) {
					if(!$rawResult) $where = array_map(function($value) {
						return (!is_array($value) AND is_array($decodedValue = json_decode($value, true))) ? $decodedValue : $value;
					}, $dataMySQL);
					return $dataMySQL;
				} else return null;
			}
		}
		/**
		 * Get last line from table
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @related OliLite::getLastInfoMySQL()
		 * @return array|null Returns last line from specified table.
		 */
		public function getLastLineMySQL($table, $rawResult = false) {
			return $this->getLastInfoMySQL($table, null, $sortBy, $rawResult);
		}
		
		/**
		 * Get infos from table
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return array|null Returns infos from specified table.
		 */
		public function getInfosMySQL($table, $whatVar = null, $where = null, $settings = null, $caseSensitive = null, $forceArray = null, $rawResult = null) {
			if(!$this->isSetupMySQL()) return null;
			else {
				/** Parameters Management */
				if(is_bool($settings)) {
					$rawResult = $forceArray;
					$forceArray = $caseSensitive;
					$caseSensitive = $settings;
					$settings = null;
				}
				if(!isset($caseSensitive)) $caseSensitive = true;
				if(!isset($forceArray)) $forceArray = false;
				if(!isset($rawResult)) $rawResult = false;
				
				/** Where Condition */
				if(in_array($where, [null, 'all', '*'], true)) $where = '1';
				else if(is_assoc($where)) $where = array_map(function($key, $value) use ($caseSensitive) {
					if(!$caseSensitive) return 'LOWER(`' . $key . '`) = \'' . strtolower(is_array($value) ? json_encode($value) : $value) . '\'';
					else return '`' . $key . '` = \'' . (is_array($value) ? json_encode($value) : $value) . '\'';
				}, array_keys($where), array_values($where));
				
				if(!empty($where)) {
					/** Additional Settings */
					$whereGlue = [];
					if(!empty($settings)) {
						if(is_assoc($settings)) {
							$settings = array_filter($settings);
							if(isset($settings['order_by'])) $settings[] = 'ORDER BY ' . array_pull($settings, 'order_by');
							if(isset($settings['limit'])) {
								if(isset($settings['from'])) $settings[] = 'LIMIT ' . array_pull($settings, 'limit') . ' OFFSET ' . array_pull($settings, 'from');
								else if(isset($settings['offset'])) $settings[] = 'LIMIT ' . array_pull($settings, 'limit') . ' OFFSET ' . array_pull($settings, 'offset');
								else $settings[] = 'LIMIT ' . array_pull($settings, 'limit');
							}
							// $startFromId = (isset($settings['fromId']) AND $settings['fromId'] > 0) ? $settings['fromId'] : 1;
							
							if(isset($settings['where_and'])) $whereGlue = array_pull($settings, 'where_and') ? ' AND ' : ' OR ';
							else if(isset($settings['where_or'])) $whereGlue = array_pull($settings, 'where_or') ? ' OR ' : ' AND ';
						} else if(!is_array($settings)) $settings = [$settings];
					}
					
					/** Data Processing */
					$dataMySQL = $this->getDataMySQL($table, $whatVar, 'WHERE ' . (is_array($where) ? implode($whereGlue ?: ' AND ', $where) : $where), !empty($settings) ? implode(' ', $settings) : null);
					if(!empty($dataMySQL) AND is_array($dataMySQL)) {
						if(count($dataMySQL) == 1) $dataMySQL = $dataMySQL[0];
						if(!$rawResult) $dataMySQL = array_map(function($value) {
							if(is_array($value) AND count($value) == 1) $value = array_values($value)[0];
							if(is_array($value)) return array_map(function($value) {
									if(!is_array($value) AND is_array($decodedValue = json_decode($value, true))) return $decodedValue;
									else return $value;
								}, $value);
							else if(is_array($decodedValue = json_decode($value, true))) return $decodedValue;
							else return $value;
						}, $dataMySQL);
						return ($forceArray OR count($dataMySQL) > 1) ? $dataMySQL : array_values($dataMySQL)[0];
					} else return null;
				} else return null;
			}
		}
		
		/**
		 * Get lines from table
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @related OliLite::getInfosMySQL()
		 * @return array|null Returns lines from specified table.
		 */
		public function getLinesMySQL($table, $where = null, $settings = null, $caseSensitive = null, $forceArray = null, $rawResult = null) {
			return $this->getInfosMySQL($table, null, $where, $settings, $caseSensitive, $forceArray, $rawResult);
		}
		
		/**
		 * Get summed infos from table
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @related OliLite::getInfosMySQL()
		 * @return numeric|boolean|null Returns summed infos if numeric values are found, false otherwise. Returns null if no MySQL infos is found.
		 */
		public function getSummedInfosMySQL($table, $whatVar = null, $where = null, $settings = null, $caseSensitive = null) {
			if(!$this->isSetupMySQL()) return null;
			else {
				$infosMySQL = $this->getInfosMySQL($table, $whatVar, $where, $settings, $caseSensitive, true);
				if(!empty($infosMySQL)) {
					$summedInfos = null;
					foreach($infosMySQL as $eachValue) {
						if(is_numeric($eachValue)) $summedInfos += $eachInfo;
					}
				} else $summedInfos = false;
				return $summedInfos;
			}
		}
		
		/**
		 * Is exist infos in table
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @related OliLite::getInfosMySQL()
		 * @return integer|boolean Returns the number of infos found, false if none found.
		 */
		public function isExistInfosMySQL($table, $where = null, $settings = null, $caseSensitive = null) {
			$result = $this->getInfosMySQL($table, 'COUNT(1)', $where, $settings, $caseSensitive);
			return $result === null ? null : (int) $result ?: false;
		}
		
		/**
		 * Is empty infos in table
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @related OliLite::getInfosMySQL()
		 * @return array|null Returns true if infos are empty, false otherwise.
		 */
		public function isEmptyInfosMySQL($table, $whatVar = null, $where = null, $settings = null, $caseSensitive = null) {
			return empty($this->getInfosMySQL($table, $whatVar, $where, $settings, $caseSensitive));
		}
		
		/** ------------- */
		/**  V. 3. Write  */
		/** ------------- */
		
		/**
		 * Insert line in table
		 * 
		 * @param string $table Table to insert line into
		 * @param array $matches Data to insert into the table
		 * 
		 * @uses OliLite::isSetupMySQL() to check the MySQL connection
		 * @uses OliLite::$db to execute SQL requests
		 * @return boolean Return true if the request succeeded, false otherwise
		 */
		public function insertLineMySQL($table, $matches) {
			if(!$this->isSetupMySQL()) return null;
			else {
				foreach($matches as $matchKey => $matchValue) {
					$queryVars[] = $matchKey;
					$queryValues[] = ':' . $matchKey;
					
					$matchValue = (is_array($matchValue)) ? json_encode($matchValue, JSON_FORCE_OBJECT) : $matchValue;
					$matches[$matchKey] = $matchValue;
				}
				$query = $this->db->prepare('INSERT INTO ' . $table . '(' . implode(', ', $queryVars) . ') VALUES(' . implode(', ', $queryValues) . ')');
				return $query->execute($matches) ?: $query->errorInfo();
			}
		}
		
		/**
		 * Update infos from table
		 * 
		 * @param string $table Table to update infos from
		 * @param array $what What to replace data with
		 * @param string|array $where Where to update data
		 * 
		 * @uses OliLite::isSetupMySQL() to check the MySQL connection
		 * @uses OliLite::$db to execute SQL requests
		 * @return boolean Return true if the request succeeded, false otherwise
		 */
		public function updateInfosMySQL($table, $what, $where) {
			if(!$this->isSetupMySQL()) return null;
			else {
				$matches = [];
				foreach($what as $whatVar => $whatValue) {
					$queryWhat[] = $whatVar . ' = :what_' . $whatVar;
					
					$whatValue = (is_array($whatValue)) ? json_encode($whatValue, JSON_FORCE_OBJECT) : $whatValue;
					$matches['what_' . $whatVar] = $whatValue;
				}
				if($where != 'all') {
					foreach($where as $whereVar => $whereValue) {
						$queryWhere[] = $whereVar . ' = :where_' . $whereVar;
						
						$whereValue = (is_array($whereValue)) ? json_encode($whereValue, JSON_FORCE_OBJECT) : $whereValue;
						$matches['where_' . $whereVar] = $whereValue;
					}
				}
				$query = $this->db->prepare('UPDATE ' . $table . ' SET '  . implode(', ', $queryWhat) . ($where != 'all' ? ' WHERE ' . implode(' AND ', $queryWhere) : ''));
				return $query->execute($matches) ?: $query->errorInfo();
			}
		}
		
		/**
		 * Delete lines from a table
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return boolean Returns true if the request succeeded, false otherwise.
		 */
		public function deleteLinesMySQL($table, $where) {
			if(!$this->isSetupMySQL()) return null;
			else {
				if(is_array($where)) {
					$matches = [];
					foreach($where as $whereVar => $whereValue) {
						$queryWhere[] = $whereVar . ' = :' . $whereVar;
						
						$whereValue = (is_array($whereValue)) ? json_encode($whereValue, JSON_FORCE_OBJECT) : $whereValue;
						$matches[$whereVar] = $whereValue;
					}
				}
				$query = $this->db->prepare('DELETE FROM ' . $table . ' WHERE ' .
				(is_array($where) ? implode(' AND ', $queryWhere) : ($where !== 'all' ? $where : '*')));
				return $query->execute($matches) ?: $query->errorInfo();
			}
		}
		
		/** ---------------------- */
		/**  V. 4. Database Edits  */
		/** ---------------------- */
		
			/** ----------------- */
			/**  V. 4. A. Tables  */
			/** ----------------- */
		
			/**
			 * Create new table
			 * 
			 * @param string $table Table to insert data into
			 * @param array $columns Columns to insert into the table
			 * 
			 * @uses OliLite::isSetupMySQL() to check the MySQL connection
			 * @uses OliLite::$db to execute SQL requests
			 * @return boolean Return true if the request succeeded, false otherwise
			 */
			public function createTableMySQL($table, $columns) {
				if(!$this->isSetupMySQL()) return null;
				else {
					foreach($columns as $matchName => $matchOption) {
						$queryData[] = $matchName . ' ' . $matchOption;
					}
					$query = $this->db->prepare('CREATE TABLE ' . $table . '(' . implode(', ', $queryData) . ')');
					return $query->execute() ?: $query->errorInfo();
				}
			}
			
			/**
			 * Is Exist MySQL Table
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return boolean Returns true if it exists.
			 */
			public function isExistTableMySQL($table) {
				if(!$this->isSetupMySQL()) return null;
				else {
					$query = $this->db->prepare('SELECT 1 FROM ' . $table);
					return $query->execute() !== false;
				}
			}
			
			/**
			 * Clear table data
			 * 
			 * Delete everything in the table but not the table itself
			 * 
			 * @param string $table Table to delete data from
			 * 
			 * @uses OliLite::isSetupMySQL() to check the MySQL connection
			 * @uses OliLite::$db to execute SQL requests
			 * @return boolean Return true if the request succeeded, false otherwise
			 */
			public function clearTableMySQL($table) {
				if(!$this->isSetupMySQL()) return null;
				else {
					$query = $this->db->prepare('TRUNCATE TABLE ' . $table);
					return $query->execute() ?: $query->errorInfo();
				}
			}
			
			/**
			 * Delete table
			 * 
			 * @param string $table Table to delete
			 * 
			 * @uses OliLite::isSetupMySQL() to check the MySQL connection
			 * @uses OliLite::$db to execute SQL requests
			 * @return boolean Return true if the request succeeded, false otherwise
			 */
			public function deleteTableMySQL($table) {
				if(!$this->isSetupMySQL()) return null;
				else {
					$query = $this->db->prepare('DROP TABLE ' . $table);
					return $query->execute() ?: $query->errorInfo();
				}
			}
			
			/** ------------------ */
			/**  V. 4. B. Columns  */
			/** ------------------ */
			
			/**
			 * Add column to table
			 * 
			 * @param string $table Table to insert column into
			 * @param string $column Column to insert into the table
			 * @param string $type Type to set for the column
			 * 
			 * @uses OliLite::isSetupMySQL() to check the MySQL connection
			 * @uses OliLite::$db to execute SQL requests
			 * @return boolean Return true if the request succeeded, false otherwise
			 */
			public function addColumnTableMySQL($table, $column, $type) {
				if(!$this->isSetupMySQL()) return null;
				else {
					$query = $this->db->prepare('ALTER TABLE ' . $table . ' ADD ' . $column . ' ' . $type);
					return $query->execute() ?: $query->errorInfo();
				}
			}
			
			/**
			 * Update column from table
			 * 
			 * @param string $table Table to update column from
			 * @param string $column Column to update from the table
			 * @param string $type Type to set for the column
			 * 
			 * @uses OliLite::isSetupMySQL() to check the MySQL connection
			 * @uses OliLite::$db to execute SQL requests
			 * @todo Add PostgreSQL support
			 * @return boolean Return true if the request succeeded, false otherwise
			 */
			public function updateColumnTableMySQL($table, $column, $type) {
				if(!$this->isSetupMySQL()) return null;
				else {
					$query = $this->db->prepare('ALTER TABLE ' . $table . ' MODIFY ' . $column . ' ' . $type);
					return $query->execute() ?: $query->errorInfo();
				}
			}
			
			/**
			 * Rename column from table
			 * 
			 * @param string $table Table to rename column from
			 * @param array $oldColumn Row to rename from the table
			 * @param string $newColumn New column name
			 * @param string|void $type Type to set for the column
			 * 
			 * @uses OliLite::isSetupMySQL() to check the MySQL connection
			 * @uses OliLite::$db to execute SQL requests
			 * @return boolean Return true if the request succeeded, false otherwise
			 */
			public function renameColumnTableMySQL($table, $oldColumn, $newColumn, $type = null) {
				if(!$this->isSetupMySQL()) return null;
				else {
					$query = $this->db->prepare('ALTER TABLE ' . $table . (isset($type) ? ' CHANGE ' : ' RENAME COLUMN ') . $oldColumn . (isset($type) ? ' ' : ' TO ') . $newColumn . (isset($type) ? ' ' . $type : ''));
					return $query->execute() ?: $query->errorInfo();
				}
			}
			
			/**
			 * Delete column from table
			 * 
			 * @param string $table Table to delete column from
			 * @param array $column Column to delete from the table
			 * 
			 * @uses OliLite::isSetupMySQL() to check the MySQL connection
			 * @uses OliLite::$db to execute SQL requests
			 * @todo Add PostgreSQL support
			 * @return boolean Return true if the request succeeded, false otherwise
			 */
			public function deleteColumnTableMySQL($table, $column) {
				if(!$this->isSetupMySQL()) return null;
				else {
					$query = $this->db->prepare('ALTER TABLE ' . $table . ' DROP ' . $column . ')');
					return $query->execute() ?: $query->errorInfo();
				}
			}
	
	/** *** *** */
	
	/** ------------- */
	/**  VI. General  */
	/** ------------- */
		
		/** --------------------- */
		/**  VI. 1. Content Load  */
		/** --------------------- */
		
		/**
		 * Load page content
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return string|void Returns the path to the file to include.
		 */
		public function loadContent(array $params = null) {
			$params = !empty($params) ? $params : $this->getUrlParam('params');
			$contentStatus = null;
			$found = null;
			
			if(!empty($params)) {
				$accessAllowed = null;
				$fileName = [];
				
				foreach($params as $eachParam) {
					if(empty($eachParam)) break; // Filename can't be empty.
					else {
						$fileName[] = $eachParam;
						$fileNameParam = implode('/', $fileName);
						
						/** User Scripts */
						if(file_exists(SCRIPTSPATH . $fileNameParam)) {
							$found = SCRIPTSPATH . $fileNameParam;
							$this->fileNameParam = $fileNameParam;
							$this->setContentType('JSON');
							break;
						
						/** User Assets */
						} else if($fileNameParam == ($this->config['assets_folder'] ?: 'assets')) {
							$accessAllowed = false; // 403 Forbidden
							break;
						
						/** User Pages */
						} else {
							/** Custom Page */
							if(file_exists(THEMEPATH . $fileNameParam . '.php')) {
								$found = THEMEPATH . $fileNameParam . '.php';
								$this->fileNameParam = $fileNameParam;
							
							/** Home Page */
							} else if($fileNameParam == 'home' AND file_exists(THEMEPATH . 'index.php')) {
								$found = THEMEPATH . 'index.php';
								$contentStatus = 'index';
							}
							
							/** Search for sub-directory */
							if(!file_exists(THEMEPATH . $fileNameParam . '/')) break; // No more to search.
							else {
								if(file_exists(THEMEPATH . $fileNameParam . '/index.php')) {
									$found = THEMEPATH . $fileNameParam . '/index.php';
									$this->fileNameParam = $fileNameParam;
								}
								continue; // There may be another level.
							}
						
						}
					}
				}
			}
			
			/** Page found / 200 OK */
			if(!empty($found)) {
				http_response_code(200); // 200 OK
				$this->contentStatus = $contentStatus ?: 'found';
				return $found;
			
			/** Access Forbidden / 403 Forbidden */
			} else if(isset($accessAllowed) AND !$accessAllowed) {
				http_response_code(403); // 403 Forbidden
				$this->contentStatus = '403';
				
				if(file_exists(THEMEPATH . '403.php')) return THEMEPATH . '403.php';
				else die('Error 403: Access forbidden');
			
			/** Page not found / 404 Not Found */
			} else {
				http_response_code(404); // 404 Not Found
				$this->contentStatus = '404';
				
				if(file_exists(THEMEPATH . '404.php')) return THEMEPATH . '404.php';
				else die('Error 404: File not found');
			}
		}
		
		/**
		 * Get content status
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @deprecated OliLite::$contentStatus variable should be read instead.
		 * @return string|void Returns the path to the file to include.
		 */
		public function getContentStatus() { return $this->contentStatus; }
		
		/** ----------------- */
		/**  VI. 2. Settings  */
		/** ----------------- */
		
		/**
		 * Get Settings Tables
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @deprecated Directly accessible with OliLite::$config
		 * @return array Returns the settings tables.
		 */
		public function getSettingsTables() { return $this->config['settings_tables'] ?: ['settings']; }
		
		/**
		 * Get Setting
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return string|boolean Returns the requested setting if succeeded.
		 */
		public function getSetting($setting, $depth = 0) {
			$isExist = [];
			if($this->isSetupMySQL()) {
				$tables = $this->config['settings_tables'] ?: ['settings'];
				foreach(($depth > 0 AND count($tables) > $depth) ? array_slice($tables, $depth) : $tables as $eachTable) {
					if($this->isExistTableMySQL($eachTable)) {
						$isExist[] = true;
						if(isset($setting)) {
							$optionResult = $this->getInfosMySQL($eachTable, 'value', array('name' => $setting));
							if(!empty($optionResult)) {
								if($optionResult == 'null') return '';
								else return $optionResult;
							}
						} else return false;
					} else $isExist[] = false;
				}
			}
			if(!in_array(true, $isExist, true)) return $this->getAppConfig($setting);
		}
		/** * @alias OliLite::getSetting() */
		public function getOption($setting, $depth = 0) { return $this->getSetting($setting, $depth); }
		
		/** ----------------------- */
		/**  VI. 3. Custom Content  */
		/** ----------------------- */
		
		/**
		 * Get Shortcut Link
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return boolean Returns true if succeeded.
		 */
		public function getShortcutLink($shortcut, $caseSensitive = false) {
			if(!empty($this->config['shortcut_links_table']) AND $this->isExistTableMySQL($this->config['shortcut_links_table'])) return $this->getInfosMySQL($this->config['shortcut_links_table'], 'url', array('name' => $shortcut), $caseSensitive);
			else return false;
		}
		
		/** ------------------- */
		/**  VI. 4. HTTP Tools  */
		/** ------------------- */
		
			/** ------------------------ */
			/**  VI. 4. A. Content Type  */
			/** ------------------------ */
			
			/**
			 * Set Content Type
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return boolean Returns new content type if succeeded.
			 */
			public function setContentType($contentType = null, $charset = null, $force = false) {
				if(!$this->contentTypeForced OR $force) {
					if($force) $this->contentTypeForced = true;
					
					if(isset($contentType)) $contentType = strtolower($contentType);
					if(!isset($contentType) OR $contentType == 'default') $contentType = 'html';
					
					if($contentType == 'html') $newContentType = 'text/html';
					else if($contentType == 'css') $newContentType = 'text/css';
					else if(in_array($contentType, ['js', 'javascript'])) $newContentType = 'text/javascript';
					else if($contentType == 'json') $newContentType = 'application/json';
					else if($contentType == 'pdf') $newContentType = 'application/pdf';
					else if($contentType == 'rss') $newContentType = 'application/rss+xml';
					else if($contentType == 'xml') $newContentType = 'text/xml';
					else if(in_array($contentType, ['debug', 'plain'])) $newContentType = 'text/plain';
					else $newContentType = $contentType;
					
					if(isset($charset)) $charset = strtolower($charset);
					if(!isset($charset) OR $charset == 'default') $charset = $this->config['default_charset'];
					
					error_reporting($contentType == 'debug' ? E_ALL : E_ALL & ~E_NOTICE);
					header('Content-Type: ' . $newContentType . ';charset=' . $charset);
					
					$this->contentType = $newContentType;
					$this->charset = $charset;
					return $newContentType;
				} else return false;
			}
			
			/**
			 * Reset Content Type
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return boolean Returns new content type if succeeded.
			 */
			public function resetContentType() { return $this->setContentType(); }
			
			/**
			 * Get Content Type
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return boolean Returns current content type.
			 */
			public function getContentType() { return $this->contentType; }
			
			/**
			 * Get Charset
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return boolean Returns current charset.
			 */
			public function getCharset() { return $this->charset; }
			
			/** ----------------------------- */
			/**  VI. 4. B. Cookie Management  */
			/** ----------------------------- */
			
				/** ----------------------------- */
				/**  VI. 4. B. a. Read Functions  */
				/** ----------------------------- */
				
				/**
				 * Get Cookie value
				 * 
				 * @version BETA-1.0.0
				 * @updated BETA-1.0.0
				 * @return boolean Returns cookie value.
				 */
				public function getCookie($name, $rawResult = false) {
					return (!$rawResult AND ($arr = json_decode($_COOKIE[$name], true)) !== null) ? $arr : $_COOKIE[$name];
				}
				
				/**
				 * Get Cookie raw value
				 * 
				 * @version BETA-1.0.0
				 * @updated BETA-1.0.0
				 * @return boolean Returns cookie raw value.
				 */
				public function getRawCookie($name) {
					$this->getCookie($name, true);
				}
				
				/**
				 * Is Exist Cookie
				 * 
				 * @version BETA-1.0.0
				 * @updated BETA-1.0.0
				 * @return boolean Returns true if the cookie exists.
				 */
				public function isExistCookie($name) {
					return isset($_COOKIE[$name]);
				}
				
				/**
				 * Is Empty Cookie
				 * 
				 * @version BETA-1.0.0
				 * @updated BETA-1.0.0
				 * @return boolean Returns true if the cookie is empty.
				 */
				public function isEmptyCookie($name) {
					return empty($_COOKIE[$name]);
				}
				
				/** ------------------------------ */
				/**  VI. 4. B. b. Write Functions  */
				/** ------------------------------ */
				
				/**
				 * Set Cookie
				 * 
				 * @version BETA-1.0.0
				 * @updated BETA-1.0.0
				 * @return boolean Returns true if succeeded.
				 */
				public function setCookie($name, $value, $expireDelay, $path, $domains, $secure = false, $httpOnly = false) {
					$value = (is_array($value)) ? json_encode($value, JSON_FORCE_OBJECT) : $value;
					$domains = (!is_array($domains)) ? [$domains] : $domains;
					foreach($domains as $eachDomain) {
						if(!setcookie($name, $value, $expireDelay ? time() + $expireDelay : 0, '/', $eachDomain, $secure, $httpOnly)) {
							$cookieError = true;
							break;
						}
					}
					return !$cookieError ? true : false;
				}
				
				/**
				 * Delete Cookie
				 * 
				 * @version BETA-1.0.0
				 * @updated BETA-1.0.0
				 * @return boolean Returns true if succeeded.
				 */
				public function deleteCookie($name, $path, $domains, $secure = false, $httpOnly = false) {
					$domains = (!is_array($domains)) ? [$domains] : $domains;
					foreach($domains as $eachDomain) {
						setcookie($name, null, -1, '/', $eachDomain, $secure, $httpOnly);
						if(!setcookie($name, null, -1, '/', $eachDomain, $secure, $httpOnly)) {
							$cookieError = true;
							break;
						}
					}
					return !$cookieError ? true : false;
				}
			
			/** --------------------------- */
			/**  VI. 4. D. Mail Management  */
			/** --------------------------- */
			
			/**
			 * Get default mail headers
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return array Returns the default mail headers.
			 */
			public function getDefaultMailHeaders($toString = false) {
				$mailHeaders = [
					'Reply-To: ' . $this->getUrlParam('name') . ' <contact@' . $this->getUrlParam('domain') . '>',
					'From: ' . $this->getUrlParam('name') . ' <noreply@' . $this->getUrlParam('domain') . '>',
					'MIME-Version: 1.0',
					'Content-type: text/html; charset=utf-8',
					'X-Mailer: PHP/' . phpversion()
				];
				if($toString) return implode("\r\n", $mailHeaders);
				else return $mailHeaders;
			}
		
		/** ------------------- */
		/**  VI. 5. HTML Tools  */
		/** ------------------- */
		
			/** ------------------------ */
			/**  VI. 5. A. File Loaders  */
			/** ------------------------ */
			
			/**
			 * Load CSS Stylesheet
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return void
			 */
			public function loadStyle($url, $tags = null, $loadNow = null, $minimize = null) {
				if(is_bool($tags)) {
					$minimize = $loadNow;
					$loadNow = $tags;
					$tags = null;
				}
				if(!isset($loadNow)) $loadNow = true;
				if(!isset($minimize)) $minimize = false;
				
				if($minimize AND empty($tags)) $codeLine = '<style type="text/css">' . $this->minimizeStyle(file_get_contents($url)) . '</style>';
				else $codeLine = '<link rel="stylesheet" type="text/css" href="' . $url . '" ' . ($tags ?: '') . '>';
				
				if($loadNow) echo $codeLine . PHP_EOL;
				else $this->htmlLoaderList[] = $codeLine;
			}
			
			/**
			 * Load Local CSS Stylesheet
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return void
			 */
			public function loadLocalStyle($url, $tags = null, $loadNow = null, $minimize = null) {
				$this->loadStyle($this->getAssetsUrl() . $url, $tags, $loadNow, $minimize);
			}
			
			/**
			 * Load CDN CSS Stylesheet
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return void
			 */
			public function loadCdnStyle($url, $tags = null, $loadNow = null, $minimize = null) {
				$this->loadStyle($this->config['cdn_url'] . $url, $tags, $loadNow, $minimize);
			}
			
			/**
			 * Load JS Script
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return void
			 */
			public function loadScript($url, $tags = null, $loadNow = null, $minimize = null) {
				if(is_bool($tags)) {
					$minimize = $loadNow;
					$loadNow = $tags;
					$tags = null;
				}
				if(!isset($loadNow)) $loadNow = true;
				if(!isset($minimize)) $minimize = false;
				
				if($minimize AND empty($tags)) $codeLine = '<script type="text/javascript">' . $this->minimizeScript(file_get_contents($url)) . '</script>';
				else $codeLine = '<script type="text/javascript" src="' . $url . '" ' . ($tags ?: '') . '></script>';
				
				if($loadNow) echo $codeLine . PHP_EOL;
				else $this->htmlLoaderList[] = $codeLine;
			}
			
			/**
			 * Load Local JS Script
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return void
			 */
			public function loadLocalScript($url, $tags = null, $loadNow = null, $minimize = null) {
				$this->loadScript($this->getAssetsUrl() . $url, $tags, $loadNow, $minimize);
			}
			
			/**
			 * Load CDN JS Script
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return void
			 */
			public function loadCdnScript($url, $tags = null, $loadNow = null, $minimize = null) {
				$this->loadScript($this->config['cdn_url'] . $url, $tags, $loadNow, $minimize);
			}
			
			/**
			 * Load End HTML Files
			 * 
			 * Force the loader list files to load
			 * 
			 * @version BETA-1.0.0
			 * @updated BETA-1.0.0
			 * @return void
			 */
			public function loadEndHtmlFiles() {
				if(!empty($this->htmlLoaderList)) {
					echo PHP_EOL;
					foreach($this->htmlLoaderList as $eachCodeLine) {
						echo array_shift($this->htmlLoaderList) . PHP_EOL;
					}
				}
			}
		
			/** --------------------------- */
			/**  VI. 5. B. File Minimizers  */
			/** --------------------------- */
			
			/**
			 * Minimize stylesheet
			 * 
			 * @param string $styleCode Stylesheet code to minimize
			 * 
			 * @return string Stylesheet code minimized
			 */
			public function minimizeStyle($styleCode) {
				$styleCode = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $styleCode);
				$styleCode = preg_replace('!\s+!', ' ', $styleCode);
				$styleCode = str_replace(': ', ':', $styleCode);
				$styleCode = str_replace(["\r\n", "\r", "\n", "\t"], '', $styleCode);
				$styleCode = str_replace(';}', '}', $styleCode);
				return $styleCode;
			}
			
			/**
			 * Minimize script
			 * 
			 * @param string $scriptCode Script code to minimize
			 * 
			 * @return string Script code minimized
			 */
			public function minimizeScript($scriptCode) {
				$scriptCode = preg_replace('!^[ \t]*/\*.*?\*/[ \t]*[\r\n]!s', '', $scriptCode);
				$scriptCode = preg_replace('![ \t]*[^:]//.*[ \t]*[\r\n]?!', '', $scriptCode);
				$scriptCode = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $scriptCode);
				$scriptCode = preg_replace('!\s+!', ' ', $scriptCode);
				$scriptCode = str_replace([' {', ' }', '{ ', '; '], ['{', '}', '{', ';'], $scriptCode);
				$scriptCode = str_replace(["\r\n", "\r", "\n", "\t"], '', $scriptCode);
				return $scriptCode;
			}
		
		/** ---------------------- */
		/**  VI. 6. Url Functions  */
		/** ---------------------- */
		
		/**
		 * Get Url Parameter
		 * 
		 * $param supported values:
		 * - null 'full' => Full Url (e.g. 'http://hello.example.com/page/param')
		 * - 'protocol' => Get url protocol (e.g. 'https')
		 * - 'base' => Get base url (e.g. 'http://hello.example.com/')
		 * - 'allbases' => Get all bases urls (e.g. ['http://hello.example.com/', 'http://example.com/'])
		 * - 'alldomains' => Get all domains (e.g. ['hello.example.com', 'example.com'])
		 * - 'fulldomain' => Get domain (e.g. 'hello.example.com')
		 * - 'domain' => Get main domain (e.g. 'example.com')
		 * - 'subdomain' => Get subdomains (e.g. 'hello')
		 * - 'all' => All url fragments
		 * - 'params' => All parameters fragments
		 * - 0 => Url without any parameters (same as base url)
		 * - 1 => First parameter: file name parameter (e.g. 'page')
		 * - # => Other parameters (e.g. 2 => 'param')
		 * - 'last' => Get the last parameters fragment
		 * - 'get' => Get $_GET
		 * - 'getvars' => Get raw GET vars
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return string|void Returns requested url param if succeeded.
		 */
		public function getUrlParam($param = null, &$hasUsedHttpHostBase = false) {
			if($param === 'get') return $_GET;
			else {
				$protocol = (!empty($_SERVER['HTTPS']) OR $this->config['force_https']) ? 'https' : 'http';
				$urlPrefix = $protocol . '://';
				
				if(!isset($param) OR $param < 0 OR $param === 'full') return $urlPrefix . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				else if($param === 'protocol') return $protocol;
				else {
					$urlSetting = $this->getSetting('url');
					$urlSetting = !empty($urlSetting) ? (!is_array($urlSetting) ? [$urlSetting] : $urlSetting) : null;
					
					if(in_array($param, ['allbases', 'alldomains'], true)) {
						$allBases = $allDomains = [];
						foreach($urlSetting as $eachUrl) {
							preg_match('/^(https?:\/\/)?(((?:[w]{3}\.)?(?:[\da-z\.-]+\.)*(?:[\da-z-]+\.(?:[a-z\.]{2,6})))\/?(?:.)*)/', $eachUrl, $matches);
							$allBases[] = ($matches[1] ?: $urlPrefix) . $matches[2];
							$allDomains[] = $matches[3];
						}
						
						if($param === 'allbases') return $allBases;
						else if($param === 'alldomains') return $allDomains;
					} else {
						$httpParams = explode('?', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 2);
						if($param === 'getvars' AND !empty($httpParams[1])) return explode('&', $httpParams[1]);
						else {
							$fractionedUrl = explode('/', $httpParams[0]);
							unset($httpParams);
							
							$baseUrlMatch = false;
							$baseUrl = $urlPrefix;
							$shortBaseUrl = ''; // IS THIS USEFULL? - It seems.
							$countLoop = 0;
							
							if(isset($urlSetting)) {
								foreach($fractionedUrl as $eachPart) {
									if(in_array($baseUrl, $urlSetting) OR in_array($shortBaseUrl, $urlSetting)) {
									// if(in_array($baseUrl, $urlSetting)) {
										$baseUrlMatch = true;
										break;
									} else {
										$baseUrlMatch = false;
										$baseUrl .= urldecode($eachPart) . '/';
										$shortBaseUrl .= urldecode($eachPart) . '/';
										$countLoop++;
									}
								}
							}
							
							$hasUsedHttpHostBase = false;
							if(!isset($urlSetting) OR !$baseUrlMatch) {
								$baseUrl = $urlPrefix . $_SERVER['HTTP_HOST'] . '/';
								$hasUsedHttpHostBase = true;
							}
							
							if(in_array($param, [0, 'base'], true)) return $baseUrl;
							else if(in_array($param, ['fulldomain', 'subdomain', 'domain'], true)) {
								preg_match('/^https?:\/\/(?:[w]{3}\.)?((?:([\da-z\.-]+)\.)*([\da-z-]+\.(?:[a-z\.]{2,6})))\/?/', $baseUrl, $matches);
								if($param === 'fulldomain') return $matches[1];
								if($param === 'subdomain') return $matches[2];
								if($param === 'domain') return $matches[3];
							} else {
								$newFractionedUrl[] = $baseUrl;
								if(!empty($this->fileNameParam)) {
									while(isset($fractionedUrl[$countLoop])) {
										if(!empty($fileName) AND implode('/', $fileName) == $this->fileNameParam) break;
										else {
											$fileName[] = urldecode($fractionedUrl[$countLoop]);
											$countLoop++;
										}
									}
									
									preg_match('/^([^?]*)(?:\?(.*))?$/', implode('/', $fileName), $matches);
									if(empty($newFractionedUrl[] = !empty($matches) ? $matches[1] : implode('/', $fileName))) array_pop($newFractionedUrl);
								}
								
								if($hasUsedHttpHostBase) $countLoop = 1; // Needs more debug..
								while(isset($fractionedUrl[$countLoop])) {
									if(!empty($fractionedUrl[$countLoop]) OR isset($fractionedUrl[$countLoop + 1])) {
										$nextFractionedUrl = urldecode($fractionedUrl[$countLoop]);
										while(isset($fractionedUrl[$countLoop + 1]) AND empty($fractionedUrl[$countLoop + 1]) AND isset($fractionedUrl[$countLoop + 2])) {
											$nextFractionedUrl .= '/' . urldecode($fractionedUrl[$countLoop + 2]);
											$countLoop += 2;
										}
										
										if(empty($newFractionedUrl[] = (preg_match('/^([^?]*)(?:\?(.*))?$/', $nextFractionedUrl, $matches) AND !empty($matches)) ? $matches[1] : !$nextFractionedUrl)) array_pop($newFractionedUrl);
									}
									$countLoop++;
								}
								
								$newFractionedUrl[1] = $newFractionedUrl[1] ?: 'home';
								
								if($param === 'all') return $newFractionedUrl;
								else if($param === 'params') return array_slice($newFractionedUrl, 1);
								else if($param === 'last') return $newFractionedUrl[count($newFractionedUrl) - 1];
								else if(isset($newFractionedUrl[$param])) return $newFractionedUrl[$param];
								else return null;
							}
						}
					}
				}
			}
		}
		
		/**
		 * Get Full Url
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return string|void Returns the assets url.
		 */
		public function getFullUrl() { return $this->getUrlParam('full'); }
		
		/**
		 * Get Content Url
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return string|void Returns the content url.
		 */
		public function getContentUrl() {
			return $this->getUrlParam(0) . 'content/';
		}
		
		/**
		 * Get Assets Url
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return string|void Returns the assets url.
		 */
		public function getAssetsUrl() {
			return $this->getUrlParam(0) . 'content/assets/';
		}
		
		/**
		 * Get CDN Url
		 * 
		 * @version BETA-1.0.0
		 * @updated BETA-1.0.0
		 * @return string|void Returns the CDN url.
		 */
		public function getCdnUrl() { return $this->config['cdn_url']; }
		
		/** ---------------------- */
		/**  VI. 7. Utility Tools  */
		/** ---------------------- */
		
			/** ---------------------- */
			/**  VI. 7. A. Generators  */
			/** ---------------------- */
		
				/** ------------------- */
				/**  VI. 7. A. a. UUID  */
				/** ------------------- */
				
				/**
				 * UUID v4 Generator Script
				 * 
				 * From https://stackoverflow.com/a/15875555/5255556
				 * 
				 * @version BETA-1.0.0
				 * @updated BETA-1.0.0
				 * @return string Returns the generated UUID.
				 */
				public function uuid4() {
					if(function_exists('random_bytes')) $data = random_bytes(16);
					else $data = openssl_random_pseudo_bytes(16);
					
					$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
					$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
					return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
				}
				
				/**
				 * Alternative UUID Generation Script
				 * 
				 * This is an alternative version of a randomly generated UUID, based on both timestamp and pseudo-random bytes.
				 * 
				 * This generated UUID format is {oooooooo-oooo-Mooo-Nxxx-xxxxxxxxxxxx}, it concatenates:
				 * - o: The current timestamp (60 bits) (time_low, time_mid, time_high)
				 * - M: The version (4 bits)
				 * - N: The variant (2 bits)
				 * - x: Pseudo-random values (62 bits)
				 * 
				 * Based on:
				 * - Code from an UUID v1 Generation script. https://github.com/fredriklindberg/class.uuid.php/blob/c1de11110970c6df4f5d7743a11727851c7e5b5a/class.uuid.php#L220
				 * - Code from an UUID v4 Generation script. https://stackoverflow.com/a/15875555/5255556
				 * 
				 * @author Matiboux <matiboux@gmail.com>
				 * @link https://github.com/matiboux/Time-Based-Random-UUID
				 * @return string Returns the generated UUID.
				 */
				function uuidAlt($tp = null) {
					if(!empty($tp)) {
						if(is_array($tp)) $time = ($tp['sec'] * 10000000) + ($tp['usec'] * 10);
						else if(is_numeric($tp)) $time = (int) ($tp * 10000000);
						else return false;
					} else $time = (int) (gettimeofday(true) * 10000000);
					$time += 0x01B21DD213814000;
					
					$arr = str_split(dechex($time & 0xffffffff), 4); // time_low (32 bits)
					$high = intval($time / 0xffffffff);
					array_push($arr, dechex($high & 0xffff)); // time_mid (16 bits)
					array_push($arr, dechex(0x4000 | (($high >> 16) & 0x0fff))); // Version (4 bits) + time_high (12 bits)
					
					// Variant (2 bits) + Cryptographically Secure Pseudo-Random Bytes (62 bits)
					if(function_exists('random_bytes')) $random = random_bytes(8);
					else $random = openssl_random_pseudo_bytes(8);
					$random[0] = chr(ord($random[0]) & 0x3f | 0x80); // Apply variant: Set the two first bits of the random set to 10.
					
					$uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', array_merge($arr, str_split(bin2hex($random), 4)));
					return strlen($uuid) == 36 ? $uuid : false;
				}
		
				/** ------------------- */
				/**  VI. 7. A. b. Misc  */
				/** ------------------- */
				
				/** Random Number generator */
				public function rand($min = 1, $max = 100) {
					if(is_numeric($min) AND is_numeric($max)) {
						if($min > $max) $min = [$max, $max = $min][0];
						return mt_rand($min, $max);
					} else return false;
				}
				public function randomNumber($min = null, $max = null) { $this->rand($min, $max); }
				
				/** KeyGen built-in script */
				// See https://github.com/matiboux/KeyGen-Lib for the full PHP library.
				public function keygen($length = 12, $numeric = true, $lowercase = true, $uppercase = true, $special = false, $redundancy = true) {
					$charactersSet = '';
					if($numeric) $charactersSet .= '1234567890';
					if($lowercase) $charactersSet .= 'abcdefghijklmnopqrstuvwxyz';
					if($uppercase) $charactersSet .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
					if($special) $charactersSet .= '!#$%&\()+-;?@[]^_{|}';
					
					if(empty($charactersSet) OR empty($length) OR $length <= 0) return false;
					else {
						if($length > strlen($redundancy) AND !$redundancy) $redundancy = true;
						
						$keygen = '';
						while(strlen($keygen) < $length) {
							$randomCharacter = substr($charactersSet, mt_rand(0, strlen($charactersSet) - 1), 1);
							if($redundancy OR !strstr($keygen, $randomCharacter)) $keygen .= $randomCharacter;
						}
						
						return $keygen;
					}
				}
			
			/** --------------------------- */
			/**  VI. 7. B. Data Conversion  */
			/** --------------------------- */
		
			/** Convert Number */
			public function convertNumber($value, $toUnit = null, $precision = null) {
				if(preg_match('/^([\d.]+)\s?(\S*)$/i', $value, $matches)) {
					list($result, $unit) = [floatval($matches[1]), $matches[2]];
					if($unit != $toUnit) {
						$unitsTable = array(
							'Yi' => 1024 ** 8,
							'Zi' => 1024 ** 7,
							'Ei' => 1024 ** 6,
							'Pi' => 1024 ** 5,
							'Ti' => 1024 ** 4,
							'Gi' => 1024 ** 3,
							'Mi' => 1024 ** 2,
							'Ki' => 1024,
							'Y' => 1000 ** 8,
							'Z' => 1000 ** 7,
							'E' => 1000 ** 6,
							'P' => 1000 ** 5,
							'T' => 1000 ** 4,
							'G' => 1000 ** 3,
							'M' => 1000 ** 2,
							'K' => 1000
						);
						
						if(!empty($unit) AND !empty($unitsTable[$unit])) $result *= $unitsTable[$unit];
						if(!empty($toUnit) AND !empty($unitsTable[$toUnit])) $result /= $unitsTable[$toUnit];
					}
					return isset($precision) ? round($result, $precision) : $result;
				} else return $value;
			}
			
			/** Convert File Size */
			public function convertFileSize($size, $toUnit = null, $precision = null) {
				if(preg_match('/^([\d.]+)\s?(\S*)$/i', $size, $matches)) {
					list($result, $unit) = [floatval($matches[1]), $matches[2]];
					if($unit != $toUnit) {
						$unitsTable = array(
							'YiB' => 1024 ** 8,
							'ZiB' => 1024 ** 7,
							'EiB' => 1024 ** 6,
							'PiB' => 1024 ** 5,
							'TiB' => 1024 ** 4,
							'GiB' => 1024 ** 3,
							'MiB' => 1024 ** 2,
							'KiB' => 1024,
							'YB' => 1000 ** 8,
							'ZB' => 1000 ** 7,
							'EB' => 1000 ** 6,
							'PB' => 1000 ** 5,
							'TB' => 1000 ** 4,
							'GB' => 1000 ** 3,
							'MB' => 1000 ** 2,
							'KB' => 1000,
							
							'Yio' => 1024 ** 8,
							'Zio' => 1024 ** 7,
							'Eio' => 1024 ** 6,
							'Pio' => 1024 ** 5,
							'Tio' => 1024 ** 4,
							'Gio' => 1024 ** 3,
							'Mio' => 1024 ** 2,
							'Kio' => 1024,
							'Yo' => 1000 ** 8,
							'Zo' => 1000 ** 7,
							'Eo' => 1000 ** 6,
							'Po' => 1000 ** 5,
							'To' => 1000 ** 4,
							'Go' => 1000 ** 3,
							'Mo' => 1000 ** 2,
							'Ko' => 1000,
							
							'Yib' => 1024 ** 8 / 8,
							'Zib' => 1024 ** 7 / 8,
							'Eib' => 1024 ** 6 / 8,
							'Pib' => 1024 ** 5 / 8,
							'Tib' => 1024 ** 4 / 8,
							'Gib' => 1024 ** 3 / 8,
							'Mib' => 1024 ** 2 / 8,
							'Kib' => 1024 / 8,
							'Yb' => 1000 ** 8 / 8,
							'Zb' => 1000 ** 7 / 8,
							'Eb' => 1000 ** 6 / 8,
							'Pb' => 1000 ** 5 / 8,
							'Tb' => 1000 ** 4 / 8,
							'Gb' => 1000 ** 3 / 8,
							'Mb' => 1000 ** 2 / 8,
							'Kb' => 1000 / 8,
							'b' => 1 / 8,
						);
						
						if(!empty($unit) AND !empty($unitsTable[$unit])) $result *= $unitsTable[$unit];
						if(!empty($toUnit) AND !empty($unitsTable[$toUnit])) $result /= $unitsTable[$toUnit];
					}
					return isset($precision) ? round($result, $precision) : $result;
				} else return $size;
			}
			
			/** ----------------------- */
			/**  VI. 7. C. Date & Time  */
			/** ----------------------- */
			
			/**
			 * Get difference between two dates
			 * 
			 * @param string $startDate Start date
			 * @param string $endDate End date
			 * @param boolean $precise Precise parameter
			 * @param boolean|void $details Details units parameter (default: true)
			 * 
			 * @return integer|array Returns date difference
			 */
			public function dateDifference($startDate, $endDate, $precise, $details = true) {
				if(is_string($startDate))
					$startDate = strtotime($startDate);
				if(is_string($endDate))
					$endDate = strtotime($endDate);
				
				$difference = abs($startDate - $endDate);
				$buffer = $difference;
				
				$results['total_seconds'] = $buffer;
				$results['seconds'] = $buffer % 60;
				
				$buffer = floor(($buffer - $results['seconds']) / 60);
				$results['total_minutes'] = $buffer;
				$results['minutes'] = $buffer % 60;
				
				$buffer = floor(($buffer - $results['minutes']) / 60);
				$results['total_hours'] = $buffer;
				$results['total_hours'] = $buffer;
				$results['hours'] = $buffer % 24;
				
				$buffer = floor(($buffer - $results['hours']) / 24);
				$results['total_days'] = $buffer;
				$results['days'] = $buffer % 365.25;
				
				$buffer = floor(($buffer - $results['months']) / 365.25);
				$results['years'] = $buffer;
				
				if($precise) {
					if(!empty($results['years']))
						return array('years' => $results['years'], 'days' => $results['days'], 'hours' => $results['hours'], 'minutes' => $results['minutes'], 'seconds' => $results['seconds']);
					else if(!empty($results['days']))
						return array('days' => $results['total_days'], 'hours' => $results['hours'], 'minutes' => $results['minutes'], 'seconds' => $results['seconds']);
					else if(!empty($results['hours']))
						return array('hours' => $results['total_hours'], 'minutes' => $results['minutes'], 'seconds' => $results['seconds']);
					else if(!empty($results['minutes']))
						return array('minutes' => $results['total_minutes'], 'seconds' => $results['seconds']);
					else
						return array('seconds' => $results['total_seconds']);
				}
				else {
					if($details) {
						if(!empty($results['years']))
							return array('years' => $results['years']);
						else if(!empty($results['total_days']))
							return array('days' => $results['total_days']);
						else if(!empty($results['total_hours']))
							return array('hours' => $results['total_hours']);
						else if(!empty($results['total_minutes']))
							return array('minutes' => $results['total_minutes']);
						else
							return array('seconds' => $results['total_seconds']);
					}
					else {
						if(!empty($results['years']))
							return $results['years'];
						else if(!empty($results['total_days']))
							return $results['total_days'];
						else if(!empty($results['total_hours']))
							return $results['total_hours'];
						else if(!empty($results['total_minutes']))
							return $results['total_minutes'];
						else
							return $results['total_seconds'];
					}
				}
			}
			
			/** ------------------------ */
			/**  VI. 7. D. Client Infos  */
			/** ------------------------ */
			
			/** Get User IP address */
			public function getUserIP() {
				if(!empty($_SERVER['REMOTE_ADDR'])) $client_ip = $_SERVER['REMOTE_ADDR'];
				else if(!empty($_ENV['REMOTE_ADDR'])) $client_ip = $_ENV['REMOTE_ADDR'];
				else $client_ip = 'unknown';
				
				if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$entries = preg_split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
					
					reset($entries);
					while(list(, $entry) = each($entries)) {
						$entry = trim($entry);
						if(preg_match('/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/', $entry, $ip_list)){
							$private_ip = [
								'/^0\./',
								'/^127\.0\.0\.1/',
								'/^192\.168\..*/',
								'/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
								'/^10\..*/'];
							
							$found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

							if($client_ip != $found_ip) {
								$client_ip = $found_ip;
								break;
							}
						}
					}
				}
				return $client_ip;
			}

}

}
?>