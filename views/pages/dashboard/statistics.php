<?php
return;

$months = array();
$series = array(
  array('name' => 'Hits'),
  array('name' => 'Unique'),
  array('name' => 'Visits')
);
ksort($statistics['date']);
foreach ($statistics['date'] as $date => $stats) {
  $date = DateTime::createFromFormat('Y-m', $date);

  $months[] = $date->format('M');
  $series[1]['data'][] = $stats['unique'];
  $series[2]['data'][] = $stats['visits'];
  $series[0]['data'][] = $stats['hits'];
}

?>
<div id="unique-chart" class="highcharts-container" style="clear:both;"></div>
<script type="text/javascript">
$(function() {
   chart = new Highcharts.Chart({
      chart: {
         renderTo: 'unique-chart',
         defaultSeriesType: 'column',
         backgroundColor: '#<?php echo $settings['service_group.HostDeploy.branding.overview-chart-background']; ?>',
         borderRadius: 20
      },
      colors: [	'#<?php echo $settings['service_group.HostDeploy.branding.overview-chart-uniques']; ?>', 
        '#<?php echo $settings['service_group.HostDeploy.branding.overview-chart-visits']; ?>', 
        '#<?php echo $settings['service_group.HostDeploy.branding.overview-chart-hits']; ?>'],
      title: {
         text: ''
      },
      xAxis: {
        categories: <?php echo json_encode($months); ?>,
        labels: {style: {color: '#<?php echo $settings['service_group.HostDeploy.branding.overview-chart-text']; ?>'}}
      },
      yAxis: {
        min: 0,
        labels: {style: {color: '#<?php echo $settings['service_group.HostDeploy.branding.overview-chart-text']; ?>'}},
        title: {text: 'Visitors', style: {color: '#<?php echo $settings['service_group.HostDeploy.branding.overview-chart-text']; ?>'}},
        allowDecimals: false
      },
      legend: {
        enabled: false
      },
      credits: { 
        enabled:false
      },
      tooltip: {
         formatter: function() {
            return ''+
               this.x +': '+ this.y +' '+this.series.name.toLowerCase();
         }
      },
      plotOptions: {
         column: {
            pointPadding: 0.2,
            borderWidth: 0
         }
      },
      series: <?php echo json_encode($series); ?>
   });


});
</script>
