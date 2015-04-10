<?php

    /* Name:Erica Huang
     * Date: 1-28-2015
     * File: Page.php
     * Purpose: template for admin pages
     */

  function createPage($value0, $value1, $value2, $value3, $value4, $value5, $value6) {
      $adminPage = <<<EOT
<div class="container">
    <div class="jumbotron header">
        <h1> {$value0}</h1>
        <p>Welcome back Admin!</p>
     </div>
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><b class="text">1. {$value4}</b></a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse">
                <div class="panel-body">
                    {$value1}
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><b class="text">2. {$value5}</b></a>
                </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse">
                <div class="panel-body">
                     {$value2}
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"><b class="text">3. {$value6}</b></a>
                </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse">
                <div class="panel-body">
                    <p> Forms that are submitted and ready to print. </p>
                    {$value3}
                </div>
            </div>
        </div>
    </div>
</div>
EOT;
        return $adminPage;
   }

?>
