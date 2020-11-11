<?php 
	class User {
		private $id;
        private $Name;
        private $Email;
		private $Password;
        private $EmailVerifiedAt;
        private $CreatedAt;
		private $UpdatedAt;
		private $tableName = 'users';
		private $dbConn;

		function setId($id) { $this->id = $id; }
        function getId() { return $this->id; }
        
		function setName($Name) { $this->Name = $Name; }
        function getName() { return $this->Name; }

        function setEmail($Email) { $this->Email = $Email; }
        function getEmail() { return $this->Email; }
        
        function setPassword($Password) { $this->Password = $Password; }
        function getPassword() { return $this->Password; }

        function setCreatedAt($CreatedAt) { $this->CreatedAt = $CreatedAt; }
        function getCreatedAt() { return $this->CreatedAt; }

        function setEmailVerifiedAt($EmailVerifiedAt) { $this->CreatedAt = $EmailVerifiedAt; }
        function getEmailVerifiedAt() { return $this->EmailVerifiedAt; }

        function setUpdatedAt($UpdatedAt) { $this->UpdatedAt = $UpdatedAt; }
        function getUpdatedAt() { return $this->UpdatedAt; }

		public function __construct() {
			$db = new database;
            $this->dbConn = $db->connect();
           
		}

		public function insert() {

            $hashedPassword = password_hash($this->getPassword(), PASSWORD_DEFAULT);
            $this->setPassword($hashedPassword);

            $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE email = :email';
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindParam(':email', $this->Email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_OBJ);


            if(empty($user)){
                
                $sql = 'INSERT INTO ' . $this->tableName . '(name, email , password, created_at) VALUES(:name, :email, :password, :created_at)';
                $stmt = $this->dbConn->prepare($sql);
                $stmt->bindParam(':name', $this->Name);
                $stmt->bindParam(':email', $this->Email);
                $stmt->bindParam(':password', $this->Password);
                $stmt->bindParam(':created_at',$this->CreatedAt);
                $stmt->execute();

                $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE email = :email';
                $stmt = $this->dbConn->prepare($sql);
                $stmt->bindParam(':email', $this->Email);
                $stmt->execute();
                $user = $stmt->fetch();
                $id = $user['id'];
                

                $sql = 'INSERT INTO ' . 'role_user'. '(role_id, user_id , user_type) VALUES(:role_id, :user_id, :user_type)'; 
/*                 $stmt = $this->dbConn->prepare($sql);
                print_r($this->pdo->errorInfo());
                $stmt->bindParam(':role_id', 3 );
                $stmt->bindParam(':user_id', $id );
                $stmt->bindParam(':user_type', 'App\Models\User');
                $stmt->execute();  */

                //http_response_code(404);
                $this->returnResponse(EMAIL_TAKEN,'User Created'); 
            }else{
                $this->returnResponse(EMAIL_TAKEN,'Email is taken'); 
            }

            
		}


        public function returnResponse($code, $data) {
			header("content-type: application/json");
			$response = json_encode(['response' =>['status'=>$code,"result" => $data]]);
			echo $response;exit;
		}
	}
 ?>