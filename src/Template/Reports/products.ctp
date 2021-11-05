<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css" integrity="sha512-/zs32ZEJh+/EO2N1b0PEdoA10JkdC3zJ8L5FTiQu82LR9S/rOQNfQN7U59U9BC12swNeRAz3HSzIL2vpp4fv3w==" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>
<div class="row">
<div class="col-md-3"></div>
    <div class="col-md-8">
        <h3 class="">Get report for products viewed and searches</h3><br>
        <form class="row row-cols-lg-auto g-3 align-items-center" method="get">
            <div class="col-12"> <label class="form-label" for="from">From:</label></div>
            <div class="col-12">
                <input value=<?php echo $_GET['from'];?> class="form-control" type="date" id="from" name="from">
            </div>
            <div class="col-12"> <label class="form-label" for="to">To:</label></div>
            <div class="col-12">
                <input value=<?php echo $_GET['to'];?>  class="form-control" type="date" id="to" name="to">
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">
                    Report
                </button>
            </div>
        </form>
    </div>
</div>

<br><h2 class="text-center">Products viewed</h2>
<canvas id="bar-chart" width="1200" height="400"></canvas>
<script>
    new Chart(document.getElementById("bar-chart"), {
        type: 'bar',
        data: {
            labels: <?php echo $days ?>,
            datasets: [{
                minBarLength: 2, 
                label: "Daily Products Views",
                backgroundColor: [
                    "#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850",
                    "#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850",
                    "#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850",
                    "#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850",
                    "#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"
                ],
                data: <?php echo $ptotal ?>
            }]
        },
    options: {
        title: {
        display: true,
        text: 'Total views'
        }
    }
    });
</script>

<?php  
    foreach($products as $day=>$value){
        if($value[0][0]['count']){ ?>
    <h4 class="text-center"><?= h($day) ?></h4>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Product ID</th>
                <th scope="col">Views</th>
            </tr>
        </thead>
        <tbody>
                <?php for($i=0; $i<count($value[0]); $i++){?>
                    <tr>
                    <td><?= h($i+1) ?></td>
                    <td><?= h($value[0][$i]['product_id']) ?></td>
                    <td><?= h($value[0][$i]['count']) ?></td>
                    </tr>
                <?php }?>
            
        </tbody>
    </table>
<?php      
        }
    }
?>

<br><h2 class="text-center">Searches</h2>
<canvas id="bar-chart2" width="1200" height="400"></canvas>
<script>
    new Chart(document.getElementById("bar-chart2"), {
        type: 'bar',
        data: {
            labels: <?echo $days ?>,
            datasets: [{
                minBarLength: 2, 
                label: "Daily Searches",
                backgroundColor: [
                    "#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850",
                    "#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850",
                    "#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850",
                    "#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850",
                    "#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"
                ],
                data: <?php echo $stotal ?>
            }]
        },
    options: {
        title: {
        display: true,
        text: 'Total search terms'
        }
    }
    });
</script>

<?php  
    foreach($searches as $day=>$value){
        if($value[0][0]['count']){
?>
    
    <h4 class="text-center"><?= h($day) ?></h4>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Search terms</th>
                <th scope="col">Views</th>
            </tr>
        </thead>
        <tbody>
                <?php for($i=0; $i<count($value[0]); $i++){?>
                    <tr>
                    <td><?= h($i+1) ?></td>
                    <td><?= h($value[0][$i]['query']) ?></td>
                    <td><?= h($value[0][$i]['count']) ?></td>
                    </tr>
                <?php }?>
            
        </tbody>
    </table>
<?php
        }
    }
?>