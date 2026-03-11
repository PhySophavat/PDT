<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <title>Document</title>
</head>
<body>
   <form class="container p-10 mt-10">
      <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Name</label>
    <input type="text" class="form-control"  name="name">
    
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Email address</label>
    <input type="email" class="form-control" name="email">
    
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Password</label>
    <input type="password" class="form-control" name="password">
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Confrim Password</label>
    <input type="password" class="form-control" name="cofrim-password">
  </div>
  
  
  <button type="submit" class="btn btn-primary">Register</button>
    <a href="{{ route('auth.login') }}">Login here</a>

</form>
   
</body>
</html>