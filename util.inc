    <?php
    // Get cURL resource
    function opts($url)
    {
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
        ));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        return json_decode($resp,true);
    }
    
    // Post curl
    function optsPost($url,$array)
    {
            $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $array
        ));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        return json_decode($resp,true);
    }
    
    //function to expireSession
    function expireSession()
    {
        if (isset($_SESSION["LAST_ACTIVITY"]))
        {
            if ((time() - $_SESSION["LAST_ACTIVITY"] )> 1800000000000000000000000000)
            {
                // last request was more than 30 minutes ago
                session_unset();     // unset $_SESSION variable for the run-time 
                session_destroy();   // destroy session data in storage
            }
            else
            {
                if (time() - $_SESSION["LAST_ACTIVITY"] > 60) {
                    $_SESSION["LAST_ACTIVITY"] = time(); // update last activity time stamp
                }
            }
            
        }
        $_SESSION['LAST_ACTIVITY'] = time(); 
    }
    
    //function set to Set roleid
    function setRoleid()
    {
        if('Student'==$_SESSION['accounttype'])
        {
            $_SESSION['roleId']=1;
        }
        else
        {
            $_SESSION['roleId']=3;
        }
    }
    ?>