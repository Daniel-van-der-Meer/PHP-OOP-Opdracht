<?php

class database{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "scholen";
    private $conn;


    function __construct()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("ERROR: could not connect with database");
        }
    }
    function getScholenFromRegio($regio){
        $json = '{"regio": "'.$regio.'"}';
        $sql = 'SELECT * FROM scholen WHERE json_data @> '.$json.' ';
        $sql_res = $this->conn->query($sql);
        $rows = array();
        while($row = $sql_res->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    function getScholen(){
        $sql = 'SELECT * FROM scholen';
        $sql_res = $this->conn->query($sql);
        $rows = array();
        while($row = $sql_res->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    function InsertSchool($json){
        $sql = "INSERT INTO scholen (id, json) VALUES ('', '".$json."')";
        $sql_res = $this->conn->query($sql);
    }

}


class school{
    public $niveau;
    public $vestigingsnaam;
    public $regio;

    function __construct($naam, $niveau, $regio) {
        $this->vestigingsnaam = $naam;
        $this->niveau = $niveau;
        $this->regio = $regio;
    }
}

$conn = new database();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $naam = $_POST["name"];
    $regio_iput = $_POST["region"];
    $regio = $regio_iput[0];
    $soort = $_POST["typeSchool"];
    $school = new school($naam, $soort, $regio);
    $json_package = json_encode($school);
    $conn->InsertSchool($json_package);
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholen lijst</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<body class="bg-light">
    
    <!-- Header -->
    <header class="p-3 mb-3 border-bottom bg-white">
        <div class="container">
          <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none px-4">
              <span class="fs-4">Scholen overzicht</span>
            </a>
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <button type="button" id="showModal" class="btn btn-primary">Toevoegen</button>
            </ul>
          </div>
        </div>
      </header>

      <!-- Modal for adding a new school -->
      <div class="modal fade" id="schoolModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">School toevoegen</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- form start -->
              <form action="" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">School naam</label>
                    <input type="text" class="form-control" id="name" name="name">
                  </div>
                  <div class="row">
                    <div class="col">
                      <label for="typeSchool" class="form-label">Soort school</label>
                      <input type="text" class="form-control" id="typeSchool" name="typeSchool">
                    </div>
                    <div class="col">
                      <label for="region" class="form-label">Regio</label>
                      <select class="form-select" id="region" name="region[]">
                        <option value="Friesland" selected>Friesland</option>
                        <option value="Groningen">Groningen</option>
                        <option value="Drenthe">Drenthe</option>
                        <option value="Overijssel">Overijssel</option>
                        <option value="Flevoland">Flevoland</option>
                        <option value="Gelderland">Gelderland</option>
                        <option value="Utrecht">Utrecht</option>
                        <option value="Noord-Holland">Noord-Holland</option>
                        <option value="Zuid-Holland">Zuid-Holland</option>
                        <option value="Zeeland">Zeeland</option>
                        <option value="Noord-Brabant">Noord-Brabant</option>
                        <option value="Limburg">Limburg</option>
                      </select>
                    </div>
                  </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sluiten</button>
              <button type="submit" class="btn btn-primary">Toevoegen</button>
            </form>
            </div>
          </div>
        </div>
      </div>

    <!-- Table here, only have to display data at this point -->
    <div class="container" >
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Filters toepoassen</button>
        <div class="dropdown-menu p-2 col-5">
          <form action="">
          <div class="regionCheck col-md-3">
            <label for="Friesland">Regios</label> <br>
              <input class="form-check-input" type="checkbox" value="Friesland" id="Friesland" checked>
              <label class="form-check-label" for="Friesland">Checked checkbox</label>
          </div>

        </form>
        </div>

        <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Naam</th>
                <th scope="col">Soort school</th>
                <th scope="col">Regio</th>
              </tr>
            </thead>

            <?php
            $scholen = $conn->getScholen();
            foreach($scholen as $school) {
                $json_data = json_decode($school['json']);

                ?>
            <tbody id="school">
                <tr>
                  <th scope="row"><?=$school['id']?></th>
                  <td><?=$json_data->vestigingsnaam?></td>
                  <td><?=$json_data->niveau?></td>
                  <td><?=$json_data->regio?></td>
                </tr>
            </tbody>
        <?php
        }
        ?>
        </table>
        <!-- End table -->

        <nav>
            <ul class="pagination justify-content-end">
              <li class="page-item">
                <a class="page-link">Previous</a>
              </li>
              <li class="page-item"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item">
                <a class="page-link" href="#">Next</a>
              </li>
            </ul>
        </nav>
    </div>

    <footer class="p-3 mb-3 border-top bg-white fixed-bottom">
        <div class="container">
          <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none px-4">
              <span class="fs-8">Opdracht door Bas, Stijn en Daniël. </span>
            </a>

          </div>
        </div>
    </footer>
      

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
<script src="main.js"></script>
</html>




