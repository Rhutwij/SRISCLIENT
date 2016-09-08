    <?php
    include_once 'util.inc';
    $base_url="http://localhost:8088/simple-service-webapp/api/";
    //colleges/4/professors
    
    //get all colleges
    function getColleges()
    {
        global $base_url;
        $url=$base_url."colleges/list";
        $colleges=opts($url);
        return $colleges;
    }
    
    //function get user info
    function getuserInfo($username)
    {
        global $base_url;
        $url=$base_url."users/$username";
        $userinfo=opts($url);
        return $userinfo;
        
    }
    
    //function get user info
    function getuserInfoById($userid)
    {
        global $base_url;
        $url=$base_url."users/info/$userid";
        $userinfo=opts($url);
        return $userinfo;
        
    }
    
    //function get user info
    function getBucketInfo($id)
    {
        global $base_url;
        $url=$base_url."users/bucket/$id";
        $bucketinfo=opts($url);
        return $bucketinfo;
        
    }
    
    //function to get professors by college
    function getProfessorsByCollegeId($id)
    {
        global $base_url;
        $professors=opts($base_url."colleges/$id/professors");
        return $professors;
    }
    
    
    //function to get college info
    function getCollegeInfo($id)
    {
        global $base_url;
        $collegeInfo=opts($base_url."colleges/$id");
        return $collegeInfo;
    }
    
    //function to get subjects by college
    function getSubjectsByCollegeId($id)
    {
        global $base_url;
        $subjects=opts($base_url."subjects/$id");
        return $subjects;
    }
    
    //function to get buckets by professorid
    function getBucketsByProfessorId($id)
    {
        global $base_url;
        $threads=opts($base_url."users/$id/threads");
        return $threads;
    }
    
    //function to get documents by bucketId
    function getDocumentsByBucketId($id,$q,$start,$sort)
    {
        global $base_url;
        if($start<0)
        {
            $start=1;
        }
        if($start>0)
        {
            $actualstart=$start-1;
            if($actualstart>=1)
            $actualstart=$actualstart."0";
        }
        $q=trim($q);
        if($q!="")
        $documents=opts($base_url."users/bucket/$id/documents?q=$q&start=$actualstart&sort=$sort");
        else
        $documents=opts($base_url."users/bucket/$id/documents?q=&start=$actualstart&sort=$sort");
        return $documents;
    }
    
    //function to get all documents based of q
    function getDocumentsGeneral($q,$username,$bucketname,$rating,$start,$sort)
    {
        //http://localhost:8088/simple-service-webapp/api/
        global $base_url;
        if($start<0)
        {
            $start=1;
        }
        if($start>0)
        {
            $actualstart=$start-1;
            if($actualstart>=1)
            $actualstart=$actualstart."0";
        }
        //echo $base_url."users/search?q=&bucketname=$bucketname&rating=$rating&username=$username&start=$actualstart&sort=$sort";exit;
        $q=trim($q);
        if($q=="")
        $documents=opts($base_url."users/search?q=&bucketname=$bucketname&rating=$rating&username=$username&start=$actualstart&sort=$sort");
        else
        $documents=opts($base_url."users/search?q=$q&bucketname=$bucketname&rating=$rating&username=$username&start=$actualstart&sort=$sort");
        return $documents;
    }
    
    
    //professorId=7&bucketId=1&subjectId=1&collegeId=4
    function paginationDocumentPage($currentPage,$totalPages,$bucketid,$professorid,$subjectid,$collegeid,$sort,$q)
    {
        echo '<nav><ul class="pagination">';
        $startPage = ($currentPage < 5)? 1 : $currentPage - 4;
        $endPage = 8 + $startPage;
        $endPage = ($totalPages < $endPage) ? $totalPages : $endPage;
        $diff = $startPage - $endPage + 8;
        $startPage -= ($startPage - $diff > 0) ? $diff : 0;
    
        if ($startPage > 1)
        {
            echo "<li><a href='documents.php?start=1&professorId=".$professorid."&bucketId=".$bucketid."&subjectId=".$subjectid."&collegeId=".$collegeid."&sort=".$sort."&q=".$q."'>First</a></li>";
            if($currentPage-1>1)
            echo "<li><a href='documents.php?start=".($currentPage-1)."&professorId=".$professorid."&bucketId=".$bucketid."&subjectId=".$subjectid."&collegeId=".$collegeid."&sort=".$sort."&q=".$q."'>Prev</a></li>";
        }
        for($i=$startPage; $i<=$endPage; $i++)
        {
             if($i==$currentPage)
            {
                echo "<li class='active' ><a href='#'>".$i."<span class='sr-only'>(current)</span></a></li>";
            }
            else
            {
                
                echo "<li><a href='documents.php?start=".$i."&professorId=$professorid&bucketId=$bucketid&subjectId=$subjectid&collegeId=$collegeid&sort=$sort&q=".$q."'>".$i."</a></li>";
            }
        }
        if ($endPage < $totalPages)
        {
            if($currentPage+1<$totalPages)
            echo "<li><a href='documents.php?start=".($currentPage+1)."&professorId=".$professorid."&bucketId=".$bucketid."&subjectId=".$subjectid."&collegeId=".$collegeid."&sort=".$sort."&q=".$q."'>Next</a></li>";
            echo "<li><a href='documents.php?start=".$totalPages."&professorId=".$professorid."&bucketId=".$bucketid."&subjectId=".$subjectid."&collegeId=".$collegeid."&sort=".$sort."&q=".$q."'>Last</a></li>";
        }
        echo ' </ul></nav>';
    }
    
    
    //professorId=7&bucketId=1&subjectId=1&collegeId=4
    //($q,$bucketname,$username,$rating,$sort,$start,$total_pages)
    function paginationDashBoard($q,$bucketname,$username,$rating,$sort,$currentPage,$totalPages)
    {
        echo '<nav><ul class="pagination">';
        $startPage = ($currentPage < 5)? 1 : $currentPage - 4;
        $endPage = 8 + $startPage;
        $endPage = ($totalPages < $endPage) ? $totalPages : $endPage;
        $diff = $startPage - $endPage + 8;
        $startPage -= ($startPage - $diff > 0) ? $diff : 0;
    
        if ($startPage > 1)
        {
            echo "<li><a href='dash.php?q=".$q."&bucketname=".$bucketname."&username=".$username."&rating=".$rating."&sort=".$sort."&start=1'>First</a></li>";
            if($currentPage-1>1)
            echo "<li><a href='dash.php?q=".$q."&bucketname=".$bucketname."&username=".$username."&rating=".$rating."&sort=".$sort."&start=".($currentPage-1)."'>Prev</a></li>";
        }
        for($i=$startPage; $i<=$endPage; $i++)
        {
             if($i==$currentPage)
            {
                echo "<li class='active' ><a href='#'>".$i."<span class='sr-only'>(current)</span></a></li>";
            }
            else
            {
                echo "<li><a href='dash.php?q=".$q."&bucketname=".$bucketname."&username=".$username."&rating=".$rating."&sort=".$sort."&start=".$i."'>".$i."</a></li>";
            }
        }
        if ($endPage < $totalPages)
        {
            if($currentPage+1<$totalPages)
            echo "<li><a href='dash.php?q=".$q."&bucketname=".$bucketname."&username=".$username."&rating=".$rating."&sort=".$sort."&start=".($currentPage+1)."'>Next</a></li>";
            echo "<li><a href='dash.php?q=".$q."&bucketname=".$bucketname."&username=".$username."&rating=".$rating."&sort=".$sort."&start=".$totalPages."'>Last</a></li>";
        }
        echo ' </ul></nav>';
    }
    
    //function to get Comments by DocumentId
    function getCommentsByDocumentId($id,$lastcommentid)
    {
        global $base_url;
        $comments=opts($base_url."users/document/$id/$lastcommentid");
        return $comments;
    }
    
    //function to get Comments by DocumentId
    function getCommentsCountByDocumentId($id)
    {
        global $base_url;
        $comments=opts($base_url."users/document/$id");
        return $comments;
    }
    
    //function for comment page pagination
    function paginationCommentPage($lastcommentid,$documentId,$totalMax,$totalMin,$minlastcommentid,$professorId,$bucketId,$subjectId,$collegeId,$q,$sort)
    {
        echo '<nav><ul class="pagination">';
        $params="&professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId&q=$q&sort=$sort";
        $minlastcommentid=(isset($_GET['previous']) && !empty($_GET['previous']) )?$_GET['previous']:$minlastcommentid;
        if($minlastcommentid>0)
        {
            $prevlastcommentid=$minlastcommentid-1;
            echo "<li><a href='comments?documentId=".$documentId."&last=".$prevlastcommentid."$params'><</a></li>";
        }
        if($lastcommentid<$totalMax)
        {
            $nextlastcommentid=$lastcommentid+1;
            //professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId&q=$q&sort=$sort&documentId=$documentId
            echo "<li><a href='comments?documentId=".$documentId."&last=".$nextlastcommentid."&previous=".$minlastcommentid."$params'>></a></li>";
        }
        echo ' </ul></nav>';
    }
    
    function addCollege($name,$type,$userid)
    {
        $url = 'http://localhost:8088/simple-service-webapp/api/colleges/addCollege';
        //Initiate cURL.
        $ch = curl_init($url);
        $jsonData=array(
        'name'=>$name,
        'type'=>$type,
        'userid'=>$userid
        );
        
        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($jsonData);
    
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
        
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        
        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
        curl_setopt($ch,CURLOPT_COOKIESESSION,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        //Execute the request
        $result = curl_exec($ch);
        //$result=str_replace("1","",$result);
        return json_decode($result,true);
    }
    
    function editCollege($name,$type,$userid,$collegeid)
    {
        $url = 'http://localhost:8088/simple-service-webapp/api/colleges/editCollege';
        //Initiate cURL.
        $ch = curl_init($url);
        $jsonData=array(
        'name'=>$name,
        'type'=>$type,
        'userid'=>$userid,
        'collegeid'=>$collegeid
        );
        
        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($jsonData);
    
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        
        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_COOKIESESSION,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        //Execute the request
        $result = curl_exec($ch);
        //$result=str_replace("1","",$result);
        return json_decode($result,true);
    }
    
    
    function deleteCollege($userid,$collegeid)
    {
        $url = 'http://localhost:8088/simple-service-webapp/api/colleges/deleteCollege';
        //Initiate cURL.
        $ch = curl_init($url);
        $jsonData=array(
        'userid'=>$userid,
        'collegeid'=>$collegeid
        );
        
        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($jsonData);
    
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        
        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_COOKIESESSION,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        //Execute the request
        $result = curl_exec($ch);
        //$result=str_replace("1","",$result);
        return json_decode($result,true);
    }
    
    
    ///for subjects
    function addSubject($name,$userid)
    {
        $url = 'http://localhost:8088/simple-service-webapp/api/subjects/addSubject';
        //Initiate cURL.
        $ch = curl_init($url);
        $jsonData=array(
        'name'=>$name,
        'userid'=>$userid
        );
        
        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($jsonData);
    
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
        
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        
        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
        curl_setopt($ch,CURLOPT_COOKIESESSION,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        //Execute the request
        $result = curl_exec($ch);
        //$result=str_replace("1","",$result);
        return json_decode($result,true);
    }
    
    function editSubject($name,$userid,$subjectid)
    {
        $url = 'http://localhost:8088/simple-service-webapp/api/subjects/editSubject';
        //Initiate cURL.
        $ch = curl_init($url);
        $jsonData=array(
        'name'=>$name,
        'userid'=>$userid,
        'subjectid'=>$subjectid
        );
        
        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($jsonData);
    
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        
        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_COOKIESESSION,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        //Execute the request
        $result = curl_exec($ch);
        //$result=str_replace("1","",$result);
        return json_decode($result,true);
    }
    
    
    function deleteSubject($userid,$subjectid)
    {
        $url = 'http://localhost:8088/simple-service-webapp/api/subjects/deleteSubject';
        //Initiate cURL.
        $ch = curl_init($url);
        $jsonData=array(
        'userid'=>$userid,
        'subjectid'=>$subjectid
        );
        
        //Encode the array into JSON.
        $jsonDataEncoded = json_encode($jsonData);
    
        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        
        //Attach our encoded JSON string to the POST fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        
        //Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch,CURLOPT_HEADER,false);
        curl_setopt($ch,CURLOPT_COOKIESESSION,false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        //Execute the request
        $result = curl_exec($ch);
        //$result=str_replace("1","",$result);
        return json_decode($result,true);
    }
    //function to get subjects by college
    function getSubjects()
    {
        global $base_url;
        $subjects=opts($base_url."subjects/list");
        return $subjects;
    }
    
    //function to get reportedDocuments;
    function getReportedDocuments()
    {
        global $base_url;
        $documents=opts($base_url."users/reportedDocuments");
        return $documents;
    }
    
    // function to getReportedComment
    function getReportedComments()
    {
        global $base_url;
        $comments=opts($base_url."users/reportedComments");
        return $comments;
    }
    
    // function to getBannedUsers
    function getBannedUsers()
    {
        global $base_url;
        $users=opts($base_url."users/list/banned");
        return $users;
    }
    
    // function to getUnBannedUsers
    function getUnBannedUsers()
    {
        global $base_url;
        $users=opts($base_url."users/list/unbanned");
        return $users;
    }
    // function to print users
    function printUsersAdminDashBoard($users)
    {
        $html="";
        foreach($users as $user)
        {
            $html.="<tr style='padding-bottom: 10em;' id=".$user['UserId'].">";
            $html.='<td>'.$user['Username'].'</td>';
            $html.='<td>'.$user['UserId'].'</td>';
            //$html.='<td><input type="text" id="role"'.$user['UserId'].' value="'.$user['RoleId'].'"></input></td>';
            $html.='<td>'.$user['Ban'].'</td>';
           // $html.='<td><button type="submit" name='."Update".$user['UserId'].' class="btn btn-success" onclick="updateRole(this);">Update</button></td>';
            if($user['Ban']==0)
            $html.='<td><button type="submit" name='."Ban".$user['UserId'].' class="btn btn-danger" onclick="banUser(this);">Ban</button></td>';
            else
            $html.='<td><button type="submit" name='."unBan".$user['UserId'].' class="btn btn-danger" onclick="unBanUser(this);">Unban</button></td>';
            $html.= '</tr>';
        }
        return $html;
    }
    
    // function to print reported documents
    function printReportedDocuments($documents)
    {
        $html="";
        foreach($documents as $document)
        {
            $html.="<tr style='padding-bottom: 10em;' id=".$document['documentId'].">";
            $html.='<td>'.$document['UserId'].' ('.$document['Username'].')</td>';
            $html.='<td>'.$document['documentId'].'</td>';
            $html.='<td>'.$document['Title'].'</td>';
            $html.='<td>'.$document['Desc'].'</td>';
            $html.='<td>'.$document['count'].'</td>';
            $html.='<td><button type="submit" name='."Hide".$document['documentId'].' class="btn btn-success" onclick="removeDoc(this);">Remove</button></td>';
            $html.='<td><button type="submit" name='."Ban".$document['UserId'].' class="btn btn-danger" onclick="HidebanUser(this,'.$document['documentId'].',\'doc\');">RemoveDoc&BanUser</button></td>';
            $html.='<td>'.$document['reason'].'</td>';
            $html.= '</tr>';
        }
        return $html;
    }
    
    // function to print reported comments
    function printReportedComments($comments)
    {
        //{"UserId":7,"commentId":34,"Comment":"sdadadafa","count":2
        $html="";
        foreach($comments as $comment)
        {
            $html.="<tr style='padding-bottom: 10em;' id=".$comment['commentId'].">";
            $html.='<td>'.$comment['UserId'].' ('.$comment['Username'].')</td>';
            $html.='<td>'.$comment['commentId'].'</td>';
            $html.='<td>'.$comment['Comment'].'</td>';
            $html.='<td>'.$comment['count'].'</td>';
            $html.='<td><button type="submit" name='."Hide".$comment['commentId'].' class="btn btn-success" onclick="removeComment(this);">Hide</button></td>';
            $html.='<td><button type="submit" name='."BanUser".$comment['UserId'].' class="btn btn-danger" onclick="HidebanUser(this,'.$comment['commentId'].',\'comment\');">Hide&BanUser</button></td>';
            $html.='<td>Inappropriate</td>';
            $html.= '</tr>';
        }
        return $html;
    }
    ?>