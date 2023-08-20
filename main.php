<?php 
session_start();
include('dbcon.php');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if(isset($_POST['save_excel_data']))
{
    $fileName = $_FILES['import_file']['name'];
    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

    $allowed_ext = ['xls','csv','xlsx'];

    if(in_array($file_ext, $allowed_ext))
    {
        $inputFileName = $_FILES['import_file']['tmp_name'];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        $data = $spreadsheet->getActiveSheet()->toArray();

        $count = "0";
        foreach($data as $row)
        {
            if($count > 0)
            {
                $id = $row['0'];
                $username = $row['1'];
                $email = $row['2'];
                $num = $row['3'];
             

                $studentQuery = "INSERT INTO user (id,username, email, num) VALUES ('$id','$username','$email', '$num')";
                $result = mysqli_query($conn, $studentQuery);
                $msg = true;
            }
            else
            {
                $count = "1";
            }
        }

        if(isset($msg))
        {
            $_SESSION['message'] = " Data Imported Successfully";
            header('Location: main.php');
            exit(0);
        }
        else
        {
            $_SESSION['message'] = "Not Imported";
            header('Location: main.php');
            exit(0);
        }
    }
    else
    {
        $_SESSION['message'] = "Invalid File";
        header('Location: main.php');
        exit(0);
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import data from excel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-4">

                <?php
                if(isset($_SESSION['message']))
                {
                    echo "<h4>".$_SESSION['message']."</h4>";
                    unset($_SESSION['message']);
                }
                ?>

             
                <div>
                    <h4>Import Excel Data into database</h4>
              
                    <form method="POST" enctype="multipart/form-data">

                        <input type="file" name="import_file" class="form-control" />
                        <button type="submit" name="save_excel_data" class="btn btn-primary mt-3" style="background-color: crimson;">Import</button>

                    </form>

                    
                </div>


                <div class ="table" style="margin-top: 40px;">

                    <h2>datatable</h2>
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                           
                           include('dbcon.php');


                            // Requête pour sélectionner les données de votre table
                            $requete = "SELECT id, username, email, num FROM user";
                            $resultat = mysqli_query($conn, $requete);

                            // Afficher les données dans le tableau
                            while ($ligne = mysqli_fetch_assoc($resultat)) {
                                echo "<tr>";
                                echo "<td>" . $ligne['id'] . "</td>";
                                echo "<td>" . $ligne['username'] . "</td>";
                                echo "<td>" . $ligne['email'] . "</td>";
                                echo "<td>" . $ligne['num'] . "</td>";
                                echo "</tr>";
                            }

                            // Fermer la connexion
                            mysqli_close($conn);
                            ?>
                        </tbody>
                        
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>