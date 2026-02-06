<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <title>SGM - Meus Chamados</title>
</head>
<body>
    <header>
        <nav class="navbar bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand text-light">SGM - Painel do Solicitante </a>
                <form class="d-flex" role="search">
                    <a class="navbar-brand text-light">Olá, Maria Solicitente | </a>
                    <button class="btn btn-outline-light" type="submit"><a class="text-light" href="api/logout.php">Sair</a></button>
                </form>
            </div>
        </nav>
    </header>
    <main class="container w-100 mt-3 ">
        <section class="ms w-100">
            <div class="tabela shadow p-3 mb-5 bg-body-tertiary rounded" >
                <table class="table">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Foto</th>
      <th scope="col">Local</th>
      <th scope="col">Descrição</th>
      <th scope="col">Data</th>
      <th scope="col">Status</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>Mark</td>
      <td>Otto</td>
      <td>@mdo</td>
      <td>12/03</td>
      <td class="bg-success">Em andamento</td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>Jacob</td>
      <td>Thornton</td>
      <td>@fat</td>
      <td>12/03</td>
      <td class="bg-success">Em andamento</td>
    </tr>
    
  </tbody>
</table>
            </div>
        </section>
    </main>
    <footer></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>    

</body>
</html>