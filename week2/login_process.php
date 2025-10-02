<?php 
//db 연결 
$db_host="localhost"; 
$db_user = "root"; 
$db_pass=""; 
$db_name="my_db"; 
$db_port = 3306; 
$conn=new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

if($conn->connect_error){
    die("데이터베이스 연결 실패: " . $conn->connect_error); // connect_error로 수정
}

//login.html에서 POST 방식으로 보낸 데이터 받기 
$username=$_POST['username']; 
$password=$_POST['password'];

// SQL 쿼리 작성(입력받은 username과 password가 일치하는 사용자 찾기) 
//$sql = "SELECT * FROM user WHERE username = '$username' AND password='$password'";
//위의 기존 코드에서는 $username, $password가 문자열 연결로 들어가서 입력을 그대로 이어 붙임 -> SQL 문법에 누가 쓴지 모르는 내용이 섞여버림

$stmt = $conn->prepare("SELECT * FROM user WHERE username = ? AND password = ?");
//prepare()이 쿼리 구조를 DB에 알려 줌, 플레이스 홀더 ? - sql문에서 값이 들어갈 자리를 표시함 (나중에 bind_param()으로 따로 넣기)

$stmt->bind_param("ss", $username, $password);
//사용자 입력값을 플레이스 홀더에 바인딩(문자열 연결 X)
//바인딩은 쿼리 틀을 DB에 먼저 보여 주고 나서, 빈칸에 들어갈 값은 문자열이 아닌 "값"으로 따로 전달
//첫 번째 인자는 타입 문자열, s=string

// 쿼리 실행 
$stmt->execute();

//실행 결과를 mysqli_result 형태로 받아 num_rows 같은 기존 코드를 그대로 쓸 수 있게 해 줌
$result = $stmt->get_result();

// 결과 확인 
if($result && $result->num_rows > 0){ // num_rows로 수정, result가 null이거나 false일 수도 있기 때문에 조건에 넣기
    //일치하는 사용자가 있으면(결과 행이 1개 이상이면)
    $user=$result->fetch_assoc(); 
    //fetch_assoc()으로 결과의 한 행을 열이름->값 형태로 꺼냄, $user['username'] 같은 것! 따라서 안전하게 값 읽기 가능
    echo "<h1>로그인 성공!</h1>";
    echo "<p>" . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . "님, 환영합니다.</p>";
    //$username을 그대로 출력하면 XSS 공격에 노출될 수 있으므로 escape처리 해 주기
}else{
    echo "<h1>로그인 실패</h1>";
    echo "<p>아이디 또는 비밀번호가 올바르지 않습니다.</p>";
    echo '<a href="login.html">다시 시도하기</a>';
}

//DB 연결 종료 
$conn->close(); 
?>