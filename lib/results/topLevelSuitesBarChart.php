<?php
/** 
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Filename $RCSfile: topLevelSuitesBarChart.php,v $
 * @version $Revision: 1.11 $
 * @modified $Date: 2008/10/28 09:54:49 $ by $Author: franciscom $
 *
 * @author	Kevin Levy
 *
 * - PHP autoload feature is used to load classes on demand
 *
 * rev: 20080511 - franciscom - refactored to manage automatically new user defined status
 *                              Removed fancy transistion
 *	20080812 - havlatm - simplyfied, polite
 *
 */
require_once('../../config.inc.php');
require_once('results.class.php');
define('PCHART_PATH','../../third_party/pchart');
include(PCHART_PATH . "/pChart/pData.class");   
include(PCHART_PATH . "/pChart/pChart.class");   

testlinkInitPage($db);
createChart($db);


/*
  function: createChart

  args :
  
  returns: 

*/
function createChart(&$dbHandler)
{
    // $tplan_mgr = new testplan($dbHandler);
    // $tproject_mgr = new testproject($dbHandler);
    // 
    // $tplan_id = $_REQUEST['tplan_id'];
    // $tproject_id = $_SESSION['testprojectID'];
    // 
    // $tplan_info = $tplan_mgr->get_by_id($tplan_id);
    // $tproject_info = $tproject_mgr->get_by_id($tproject_id);
    // 
    // $re = new results($dbHandler, $tplan_mgr, $tproject_info, $tplan_info,
    //                   ALL_TEST_SUITES,ALL_BUILDS);
    
    $topLevelSuites = $_SESSION['statistics']['getTopLevelSuites']; //$re->getTopLevelSuites();
    $mapOfAggregate = $_SESSION['statistics']['getAggregateMap']; //$re->getAggregateMap();
   
    if (is_array($topLevelSuites)) 
    {
        foreach($topLevelSuites as $tsuite )
        {
            $rmap = $mapOfAggregate[$tsuite['id']];
        	  $tsuiteNames[] = htmlspecialchars($tsuite['name']);

            unset($rmap['total']);
        	  foreach($rmap as $key => $value)
        	  {
        		    $totals[$key][]=$value;  
        	  }
        } 
    } // end if 

    $obj = new stdClass();
    $obj->xAxis=new stdClass();
    $obj->xAxis->values = $tsuiteNames;
    $obj->xAxis->serieName = 'Serie8';
    $obj->series_color = null;
    
 
    $resultsCfg = config_get('results');
    foreach( $totals as $status => $values)
    {
       $obj->chart_data[] = $values;
       $obj->series_label[] =lang_get($resultsCfg['status_label'][$status]);
       $obj->series_color[]=$resultsCfg['charts']['status_colour'][$status];
    }

    $DataSet = new pData;
    foreach($obj->chart_data as $key => $values)
    {
        $id=$key+1;
        $DataSet->AddPoint($values,"Serie{$id}");  
        $DataSet->SetSerieName($obj->series_label[$key],"Serie{$id}");
        
    }
    $DataSet->AddPoint($obj->xAxis->values,$obj->xAxis->serieName);
    $DataSet->AddAllSeries();
    $DataSet->RemoveSerie($obj->xAxis->serieName);
    $DataSet->SetAbsciseLabelSerie($obj->xAxis->serieName);

           
    // Initialise the graph
    $Test = new pChart(700,230);
    foreach( $obj->series_color as $key => $hexrgb)
    {
        $rgb=str_split($hexrgb,2);
        $Test->setColorPalette($key,hexdec($rgb[0]),hexdec($rgb[1]),hexdec($rgb[2]));  
    }
    $Test->drawGraphAreaGradient(132,173,131,50,TARGET_BACKGROUND);
    $Test->setFontProperties(PCHART_PATH . "/Fonts/tahoma.ttf",8);
    $Test->setGraphArea(120,20,675,190);
    $Test->drawGraphArea(213,217,221,FALSE);
    $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_ADDALL,213,217,221,TRUE,0,2,TRUE);
  
    // Draw the bar chart
    $Test->drawStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),70);
    
    // Draw the title
    $Title = lang_get('results_top_level_suites');
    $Test->drawTextBox(0,0,50,230,$Title,90,255,255,255,ALIGN_BOTTOM_CENTER,TRUE,0,0,0,30);
    
    // Draw the legend
    $Test->setFontProperties(PCHART_PATH . "/Fonts/tahoma.ttf",8);
    $Test->drawLegend(610,10,$DataSet->GetDataDescription(),236,238,240,52,58,82);
    
    // Render the picture
    $Test->addBorder(2);
    $Test->Stroke();
}
?>