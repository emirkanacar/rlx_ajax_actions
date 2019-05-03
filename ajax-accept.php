<?php 
    session_start();

    header('Content-Type: application/json');

    $requestCount = 10;
    $timedOut = 60;

    $https(bool) = false;

    $dataTimeInit = date("Y-m-d H:i:s");

    if(!isset($_SESSION['FIRST_REQUESTED_TIME']))
    {
        $_SESSION['FIRST_REQUESTED_TIME'] = $dataTimeInit;
    }

    $firstRequestTime = $_SESSION['FIRST_REQUESTED_TIME'];
    $timeExpire = date("Y-m-d H:i:s", strtotime($firstRequestTime)+($timedOut));
    if(!isset($_SESSION['REQ_COUNT']))
    {
        $_SESSION['REQ_COUNT'] = 0;
    }

    $reqCount = $_SESSION['REQ_COUNT'];
    $reqCount++;

    if($dataTimeInit > $timeExpire)
    {
        $reqCount = 1;
        $firstRequestTime = $dataTimeInit;
    }

    $_SESSION['REQ_COUNT'] = $reqCount;
    $_SESSION['FIRST_REQUESTED_TIME'] = $firstRequestTime;
    header('X-RateLimit-Limit: '.$requestCount);
    header('X-RateLimit-Remaining: ' . ($requestCount-$reqCount));

    try{
        $db = new PDO('mysql:host=localhost;dbname=rlx;charset=utf8','root','');
    }catch(PDOException $e){
        echo 'Error: '.$e->getMessage();
    }

    function is_ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    if(isset($_SERVER['HTTP_ORIGIN']))
    {
        if($https == true)
        {
           $address = 'https://' . $_SERVER['SERVER_NAME'];
        }else{
           $address = 'http://' . $_SERVER['SERVER_NAME'];
        }

        if(strpos($address, $_SERVER['HTTP_ORIGIN']) !== 0)
        {
            exit(json_encode([
                'error' => "Invalid request: " . $_SERVER['HTTP_ORIGIN']
            ]));
        }
        else {
            if($reqCount > $requestCount)
            {
                http_response_code(429);
                exit();
            }
            else {
                if(is_ajax())
                {
                    if(isset($_POST['type']) && !empty($_POST['type']))
                    {
                        $name = htmlspecialchars(trim($_POST['first-name']));
                        $lastname = htmlspecialchars(trim($_POST['last-name']));
                        $username = htmlspecialchars(trim($_POST['username']));
                        $email = htmlspecialchars(trim($_POST['email']));
                        
            
                        if(!isset($name) || empty($name))
                        {
                            echo json_encode(array('status' => 'error', 'message' => 'Name is empty!'), JSON_UNESCAPED_UNICODE);
                        }
                        else if(!isset($lastname) || empty($lastname))
                        {
                            echo json_encode(array('status' => 'error', 'message' => 'Lastname is empty!'), JSON_UNESCAPED_UNICODE);
                        }
                        else if(!isset($email) || empty($email))
                        {
                            echo json_encode(array('status' => 'error', 'message' => 'Email is empty!'), JSON_UNESCAPED_UNICODE);
                        }
                        else if(!isset($username) || empty($username))
                        {
                            echo json_encode(array('status' => 'error', 'message' => 'Username is empty!'), JSON_UNESCAPED_UNICODE);
                        }
                        else {
            
                            $dataSql = $db->prepare('INSERT INTO form_data (first_name,last_name,username,email) VALUES (?,?,?,?)');
                            $addData = $dataSql->execute(array($username,$lastname,$username,$email));
            
                            $id = $db->lastInsertId();
                            
                            if($addData)
                            {
                                echo json_encode(
                                    array('status' => 'success', 'message' => 'Successfully saved!', 'data' => 
                                    array('id' => $id, 'firstname' => $name, 'lastname' => $lastname, 'username' => $username, 'email' => $email)), JSON_UNESCAPED_UNICODE);
                            }
                        }
            
                    }
                    else {
                        echo json_encode(array('status' => 'oopss', 'message' => 'Invalid post request'));
                    }
                }
                else {
                    echo json_encode(array('status' => 'error', 'message' => 'This request not ajax!'));
                }
            }
        }
    }
    else {
        exit(json_encode([
            'error' => "Invalid request"
        ]));
    }
