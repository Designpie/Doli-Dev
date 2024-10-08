<?php
/* Copyright (C) 2001-2003 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2009 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Alexandre Spangaro   <aspangaro@open-dsi.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/don/stats/index.php
 *  \ingroup    donations
 *  \brief      Page with donations statistics
 */

// Load Dolibarr environment
require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/don/class/don.class.php';
require_once DOL_DOCUMENT_ROOT.'/don/class/donstats.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/dolgraph.class.php';

$WIDTH = DolGraph::getDefaultGraphSizeForStats('width');
$HEIGHT = DolGraph::getDefaultGraphSizeForStats('height');

$userid = GETPOSTINT('userid');
$socid = GETPOSTINT('socid');
// Security check
if ($user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$nowyear = dol_print_date(dol_now('gmt'), "%Y", 'gmt');
$year = GETPOST('year') > 0 ? GETPOST('year') : $nowyear;
$startyear = $year - (!getDolGlobalString('MAIN_STATS_GRAPHS_SHOW_N_YEARS') ? 2 : max(1, min(10, getDolGlobalString('MAIN_STATS_GRAPHS_SHOW_N_YEARS'))));
$endyear = $year;

// Load translation files required by the page
$langs->loadLangs(array("companies", "other", "sendings"));

// Security check
$result = restrictedArea($user, 'don');


/*
 * View
 */

$form = new Form($db);
$title = $langs->trans("DonationsStatistics");
llxHeader('', $title, '', '', 0, 0, '', '', '', 'mod-donation page-stats');

print load_fiche_titre($langs->trans("DonationsStatistics"), $mesg);


dol_mkdir($dir);

$stats = new DonationStats($db, $socid, '', ($userid > 0 ? $userid : 0));

// Build graphic number of object
$data = $stats->getNbByMonthWithPrevYear($endyear, $startyear);
//var_dump($data);exit;
// $data = array(array('Lib',val1,val2,val3),...)


if (!$user->hasRight('societe', 'client', 'voir')) {
	$filenamenb = $dir.'/donationnbinyear-'.$user->id.'-'.$year.'.png';
} else {
	$filenamenb = $dir.'/donationnbinyear-'.$year.'.png';
}

$px1 = new DolGraph();
$mesg = $px1->isGraphKo();
if (!$mesg) {
	$px1->SetData($data);
	$i = $startyear;
	$legend = array();
	while ($i <= $endyear) {
		$legend[] = $i;
		$i++;
	}
	$px1->SetLegend($legend);
	$px1->SetMaxValue($px1->GetCeilMaxValue());
	$px1->SetMinValue(min(0, $px1->GetFloorMinValue()));
	$px1->SetWidth($WIDTH);
	$px1->SetHeight($HEIGHT);
	$px1->SetYLabel($langs->trans("NbOfDonations"));
	$px1->SetShading(3);
	$px1->SetHorizTickIncrement(1);
	$px1->mode = 'depth';
	$px1->SetTitle($langs->trans("NumberOfDonationsByMonth"));

	$px1->draw($filenamenb, $fileurlnb);
}


// Show array
$data = $stats->getAllByYear();
$arrayyears = array();
foreach ($data as $val) {
	if (!empty($val['year'])) {
		$arrayyears[$val['year']] = $val['year'];
	}
}
if (!count($arrayyears)) {
	$arrayyears[$nowyear] = $nowyear;
}

$h = 0;
$head = array();
$head[$h][0] = DOL_URL_ROOT.'/don/stats/index.php';
$head[$h][1] = $langs->trans("ByMonthYear");
$head[$h][2] = 'byyear';
$h++;

$type = 'donation_stats';

complete_head_from_modules($conf, $langs, null, $head, $h, $type);

print dol_get_fiche_head($head, 'byyear', $langs->trans("Statistics"), -1);


print '<div class="fichecenter"><div class="fichethirdleft">';


// Show filter box
print '<form name="stats" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';

print '<table class="border centpercent">';
print '<tr class="liste_titre"><td class="liste_titre" colspan="2">'.$langs->trans("Filter").'</td></tr>';
// Company
print '<tr><td class="left">'.$langs->trans("ThirdParty").'</td><td class="left">';
print img_picto('', 'company', 'class="pictofixedwidth"');
print $form->select_company($socid, 'socid', '', 1, 0, 0, array(), 0, 'widthcentpercentminusx maxwidth300', '');
print '</td></tr>';
// User
print '<tr><td class="left">'.$langs->trans("CreatedBy").'</td><td class="left">';
print img_picto('', 'user', 'class="pictofixedwidth"');
print $form->select_dolusers($userid, 'userid', 1, '', 0, '', '', 0, 0, 0, '', 0, '', 'widthcentpercentminusx maxwidth300');
print '</td></tr>';
// Year
print '<tr><td class="left">'.$langs->trans("Year").'</td><td class="left">';
if (!in_array($year, $arrayyears)) {
	$arrayyears[$year] = $year;
}
if (!in_array($nowyear, $arrayyears)) {
	$arrayyears[$nowyear] = $nowyear;
}
arsort($arrayyears);
print $form->selectarray('year', $arrayyears, $year, 0, 0, 0, '', 0, 0, 0, '', 'width75');
print '</td></tr>';
print '<tr><td class="center" colspan="2"><input type="submit" name="submit" class="button small" value="'.$langs->trans("Refresh").'"></td></tr>';
print '</table>';
print '</form>';
print '<br><br>';

print '<div class="div-table-responsive-no-min">';
print '<table class="border centpercent">';
print '<tr height="24">';
print '<td class="center">'.$langs->trans("Year").'</td>';
print '<td class="right">'.$langs->trans("NbOfDonations").'</td>';
/*print '<td class="center">'.$langs->trans("AmountTotal").'</td>';
print '<td class="center">'.$langs->trans("AmountAverage").'</td>';*/
print '</tr>';

$oldyear = 0;
foreach ($data as $val) {
	$year = $val['year'];
	while (!empty($year) && $oldyear > $year + 1) { // If we have empty year
		$oldyear--;
		print '<tr height="24">';
		print '<td class="center"><a href="'.$_SERVER["PHP_SELF"].'?year='.$oldyear.'">'.$oldyear.'</a></td>';

		print '<td class="right">0</td>';
		/*print '<td class="right">0</td>';
		print '<td class="right">0</td>';*/
		print '</tr>';
	}

	print '<tr height="24">';
	print '<td class="center"><a href="'.$_SERVER["PHP_SELF"].'?year='.$year.'">'.$year.'</a></td>';
	print '<td class="right">'.$val['nb'].'</td>';
	/*print '<td class="right">'.price(price2num($val['total'],'MT'),1).'</td>';
	print '<td class="right">'.price(price2num($val['avg'],'MT'),1).'</td>';*/
	print '</tr>';
	$oldyear = $year;
}

print '</table>';
print '</div>';


print '</div><div class="fichetwothirdright">';


// Show graphs
print '<table class="border centpercent"><tr class="pair nohover"><td class="center">';
if ($mesg) {
	print $mesg;
} else {
	print $px1->show();
	print "<br>\n";
	/*print $px2->show();
	print "<br>\n";
	print $px3->show();*/
}
print '</td></tr></table>';


print '</div></div>';
print '<div class="clearboth"></div>';

print dol_get_fiche_end();



// TODO USe code similar to commande/stats/index.php instead of this one.
/*
print '<table class="border centpercent">';
print '<tr><td class="center">'.$langs->trans("Year").'</td>';
print '<td width="40%" class="center">'.$langs->trans("NbOfSendings").'</td></tr>';

$sql = "SELECT count(*) as nb, date_format(date_expedition,'%Y') as dm";
$sql.= " FROM ".MAIN_DB_PREFIX."expedition";
$sql.= " WHERE fk_statut > 0";
$sql.= " AND entity = ".$conf->entity;
$sql.= " GROUP BY dm DESC";

$resql=$db->query($sql);
if ($resql)
{
	$num = $db->num_rows($resql);
	$i = 0;
	while ($i < $num)
	{
		$row = $db->fetch_row($resql);
		$nbproduct = $row[0];
		$year = $row[1];
		print "<tr>";
		print '<td class="center"><a href="month.php?year='.$year.'">'.$year.'</a></td><td class="center">'.$nbproduct.'</td></tr>';
		$i++;
	}
}
$db->free($resql);

print '</table>';
*/

print '<br>';
print '<i>'.$langs->trans("StatsOnDonationsOnlyValidated").'</i>';

llxFooter();

$db->close();
