<?php

/**
 * Results display view for the plugin
 *
 * @since      1.0.0
 * @package    Serious_Daily_Writing_Habit\admin
 * @subpackage Serious_Daily_Writing_Habit\admin\partials
 */



// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function results_page_layout() {

	$options = get_option( 'dwh_options' );
	$target=$options['target_number_words'];
	$ndays=$options['number_days_show_habit'];
	$counts_datapoints=Serious_Daily_Writing_Habit_Admin::get_latests_increments($ndays);
	$target_datapoints=[];
	$days_datapoints=[];
	$avg_datapoints=[]; $avg=array_sum($counts_datapoints)/$ndays;
	for ($i = 0; $i < $ndays; $i++) {

		$today = new DateTime('today');
		$day_to_count=$ndays-$i-1;
		$today->modify("-$day_to_count day");
	    $days_datapoints[]=$today->format("Ymd");  // X-axis for the chart
		$target_datapoints[$i]=$target; // fixed horizontal line to help visualizing the days we reached the target
        $avg_datapoints[$i]=$avg;
	}

	echo '<div class="wrap">' . "\n";
	echo '	<h1>' . get_admin_page_title() . '</h1>' . "\n";
	?>

	<canvas id="myChart" width="400" height="400"></canvas>
	<script>
        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php echo implode( ', ', $days_datapoints ); ?>],
                datasets: [{
                    label: '# of Written Words',
                    data: [<?php echo implode( ', ', $counts_datapoints ); ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                    },
                    {
                        label: 'Writing Target',
                        data: [<?php echo implode( ', ', $target_datapoints ); ?>], // Example - data: [12, 19, 3, 5, 2, 3],
                        borderColor: 'rgba(255,99,132,1)',
                        backgroundColor: 'rgba(255,99,132,0.2)',  //only to be displayed in the small box next to the chart title
	                    fill:false,
	                    type: 'line'  //changing the type of this second dataset to a line type, see mixed charts https://www.chartjs.org/docs/latest/charts/mixed.html
                    },
                    {
                        label: 'Average',
                        data: [<?php echo implode( ', ', $avg_datapoints ); ?>],
                        borderColor: 'rgba(0,255,0,1)',
                        backgroundColor: 'rgba(0,255,0,0.2)',  //only to be displayed in the small box next to the chart title
                        fill:false,
                        type: 'line'  //changing the type of this second dataset to a line type, see mixed charts https://www.chartjs.org/docs/latest/charts/mixed.html
                    }
                ]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
	</script>

	<?php
    echo '</div>' . "\n";
}
