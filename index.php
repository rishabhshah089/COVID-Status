<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.covid19india.org/state_district_wise.json",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
));

$state = (string) ($_POST['state'] ?? '');
//$state = $_POST['state'];
$state = ucwords($state);
$state = rtrim($state," ");
$response = curl_exec($curl);

curl_close($curl);

$state_data = json_decode($response,true);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.rootnet.in/covid19-in/unofficial/covid19india.org/statewise",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
));

$response = curl_exec($curl);
$state_stats = json_decode($response,true);


curl_close($curl);

////////////////////////////////////////////////////////////////
    $cases = "";
    $error = "";

    if (array_key_exists('state', $_POST)) {

        $state = $_POST['state'];
        $state = ucwords($state);
        $state = rtrim($state," ");
        //print_r($state_stats['data']);
        //print_r($state_stats['data']['statewise']);


        foreach ($state_stats['data']['statewise'] as $key => $value) {
            if ($state_stats['data']['statewise'][$key]['state'] == $state) {
                //print_r($state_stats['data']['statewise'][$key]);
                $totalCases = $state_stats['data']['statewise'][$key]['confirmed'];
                $cured = $state_stats['data']['statewise'][$key]['recovered'];
                $casualities = $state_stats['data']['statewise'][$key]['deaths'];
            }

            else {
                $error = "That state could not be found. Please check again.";
            }
        }
        //print_r($state_stats['data']['total']['confirmed']);

        //if (state_stats[][statewise] == $state) {
        //    print_r(state_stats[$state]);
        //}

        /*$forecastPage = file_get_contents("https://www.mohfw.gov.in/");

        $pageArray = explode("<td>".$state."</td>", $forecastPage);*/

/*        if (sizeof($pageArray) > 1) {

                $secondPageArray = explode('</td>', $pageArray[1]);
                //preg_match_all('!\d+!', $secondPageArray[0], $matches);
                //echo $matches;

                if (($secondPageArray[0]) >= 0) {

                    $totalCases = $secondPageArray[0];
                    $cured = $secondPageArray[1];
                    $casualities = $secondPageArray[2];
                    //$weather = $secondPageArray[0];

                } else {

                    $error = "That state could not be found.";

                }

            } else {

                $error = "That state could not be found.";

            }*/

        //}

    }


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- displays site properly based on user's device -->

  <link rel="icon" type="image/png" sizes="32x32" href="./images/favicon-32x32.png">

  <title>COVID-19</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="styles.css">

</head>
<body>


<div class="container">

  <ul class="nav justify-content-end">
  <li class="nav-item">
    <span class="dark-mode">
      Dark Mode
    </span>
    <label class="switch">
      <input type="checkbox" checked>
      <span class="slider round"></span>
    </label>
  </li>
  </ul>

    <div class="jumbotron">
      <h1 class="display-4">Get Status</h1>

      <form method="post">
        <fieldset class="form-group">
          <label for="state">Enter the name of a state.</label>
          <input type="text" class="form-control" name="state" id="state" placeholder="Enter State Name" value = "<?php
          if (array_key_exists('state', $_POST)) {
          echo $_POST['state'];
          }
          ?>">
        </fieldset>

        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>


  <div class="grid">

    <div id="areaA">
      <div class="top">
        <span class="logo-text"><?php
              if ($totalCases) {
                  echo '<div style="font-weight:bold;"> Total Cases in '.$state.'</div><p></p>';
                  echo '<span class="logos-top">Total Cases</span>';
                  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                  echo ''.$totalCases.'';
              } else if ($error) {
                  echo ''.$error.'';
              }
              ?></span>
      </div>

      <div class="top">
        <span class="logo-text"><?php
              if ($totalCases) {
                  echo '<span class="logos-top">Recoveries</span>';
                  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                  echo ''.$cured.'';
              }
              ?></span>
      </div>

      <div class="top">
        <span class="logo-text"><?php
              if ($totalCases) {
                  echo '<span class="logos-top">Casualities</span>';
                  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                  echo ''.$casualities.'';
              }
              ?></span>
      </div>

    </div>

  </div>


  <div class="stats">

    <div id="stats-details">

      <div class="stat-head">
        <?php
          foreach($state_data[$state]['districtData'] as $key => $value){
            if ($key == 'Unknown'){
                break;
            }
            if ($key == 'Other States' || $key == 'Other State') {
                echo ''.$key.'/UTs';
            }
            else {
                echo ''.$key.'';
            }

            echo '<div class="top" style="font-weight: bold;">
            <span class="logos">Total Cases</span>
            <span class="logo-text">'.
            ($state_data[$state]['districtData'][$key]['active']+$state_data[$state]['districtData'][$key]['recovered']+$state_data[$state]['districtData'][$key]['deceased']).
            '</span></div>';

            echo '<div class="top">
            <span class="logos">Recoveries</span>
            <span class="logo-text">'.
            ($state_data[$state]['districtData'][$key]['recovered']).
            '</span></div>';

            echo '<div class="top">
            <span class="logos">Casualities</span>
            <span class="logo-text">'.
            ($state_data[$state]['districtData'][$key]['deceased']).
            '</span></div>';

            echo '<br  />';
          }
        ?>
      </div>

    </div>

  </div>


    <div class="grid">

    <div id="areaB">

      <div style="font-weight:bolder;"> Total Cases in India</div>

      <div class="top">
        <span class="logo-text"><?php
                  echo '<span class="logos-top">Total Cases</span>';
                  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                  echo ''.($state_stats['data']['total']['confirmed']).'';

              ?></span>
      </div>

      <div class="top">
        <span class="logo-text"><?php
                  echo '<span class="logos-top">Active Cases</span>';
                  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                  echo ''.($state_stats['data']['total']['active']).'';

              ?></span>
      </div>


      <div class="top">
        <span class="logo-text"><?php
                  echo '<span class="logos-top">Recoveries</span>';
                  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                  echo ''.($state_stats['data']['total']['recovered']).'';
              ?></span>
      </div>

      <div class="top">
        <span class="logo-text"><?php
                  echo '<span class="logos-top">Casualities</span>';
                  echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                  echo ''.($state_stats['data']['total']['deaths']).'';
              ?></span>
      </div>
    </div>

  </div>




  </div>


  <script type="text/javascript">

  const toggleSwitch = document.querySelector('.switch input[type="checkbox"]');

  function switchTheme(e) {
    if (e.target.checked) {
        document.documentElement.setAttribute('data-theme', 'light');
    }
    else {
      document.documentElement.setAttribute('data-theme', 'dark');
    }
  }

  toggleSwitch.addEventListener('change', switchTheme, false);

  </script>



</div>











</body>
</html>
