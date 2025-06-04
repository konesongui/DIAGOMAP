<?php 


// var_dump($data);
// var_dump($students->subject_results);
// var_dump($students->subject_by_module_groups);
// exit;



?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Bulletin de note</title>
    <style>
        .page {
            page-break-before: always;
        }

        @page {
            margin: 10px;
        }

        table {
            border-collapse: collapse;
            font-size: 11px;
        }

        table#mark tr td {
            border-collapse: collapse;
            border: 1px solid black;
            padding: 3px;
        }

        .table-header {
            background-color: <?= $sch_setting->color ?? "#A7CBF2" ?>;
            font-weight: bold;
        }

        .table-header th {
            padding: 5px;
            text-align: center;
            border: 1px solid black;
        }

        .text-center {
            text-align: center;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        #wrapper {
            width: 21cm !important;
            height: 29.7cm !important;
            position: relative;
            padding: 15px;
        }
    </style>
</head>

<body style="border: none !important" class="page">
    <?php foreach ($students as $student) : ?>
        <div id="wrapper">
            <table width="100%" border="0">
                <tbody>
                    <tr border="0">
                        <td rowspan="2" align="center" style="vertical-align: middle">
                            <img style="width: 100%; height: 80px !important;" src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>" alt="Image banniere" />
                        </td>
                        <td rowspan="3" class="text-center">
                            <?= strtoupper($sch_setting->republique) ?><br />
                            <?= strtoupper($sch_setting->ministere) ?><br /><br /><br />
                            <b><?= strtoupper($sch_setting->name) ?></b><br />
                        </td>
                        <td style="direction: rtl;" dir="rtl">
                            <b>Année Scolaire</b> : <b><?= str_replace("-", "-", $exam_details->session) ?></b>
                        </td>
                    </tr>
                    <tr>
                        <td style="direction: rtl; text-align: right" dir="rtl">
                            <b>BULLETIN DE NOTES</b><br />
                            <b><?= strtoupper($exam_details->exam) ?></b>
                        </td>
                    </tr>
                </tbody>
            </table>

            <br>
            <br>

            <table id="mark" border="0" width="100%" cellspacing="0">
                <tbody style="font-size: 12px;">
                    <tr style="font-weight: bold;">
                        <td colspan="3">
                            Téléphone : <b><?= $sch_setting->phone ?></b><br />
                            Adresse postale : <b><?= strtoupper($sch_setting->address) ?></b><br />
                        </td>
                        <td colspan="3" style="border-right: 0">
                            Code : <b><?= $sch_setting->dise_code ?></b> <br />
                            E-mail : <b><?= $sch_setting->email ?></b><br />
                        </td>
                        <td colspan="1" style="direction: rtl; border-left: 0;" dir="rtl">
                            Statut : <b><?= $sch_setting->stat ?? "Privé" ?></b>
                        </td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td colspan="1" align="center" style="vertical-align: middle; border-right: 0; border-right: 0;">
                            <img src="<?= base_url($student->image); ?>" class="img-responsive" width="60" height="60" />
                        </td>
                        <td colspan="3" style="border-right: 0; border-left: 0;">
                            Nom : <?= $student->firstname ?> <?= $student->lastname ?><br />
                            Matricule : <?= $student->roll_no ?? "N/A" ?><br />
                            Classe : <?= $section["section"] ?><br />
                            Effectif: <?= $stats["number_of_students"] ?><br />
                        </td>
                        <td colspan="2" style="border-left: 0; border-right: 0">
                            Sexe : <?= $student->gender ?><br />
                            Né(e) le : <?= date($sch_setting->date_format, strtotime($student->dob)) ?><br />
                            Pays / Nationalité : <?= $student->state ?><br />
                        </td>
                        <td colspan="1" style="border-left: 0;">
                            <p>
                                Redoublant : <?= $student->categorie == "Nred" ? "Non" : "Oui"  ?><br />
                                Régime : <?= $student->regime ?: "Non-Boursier" ?><br />
                                Statut: <?= ucwords($student->category) ?><br />
                            </p>
                        </td>
                    </tr>
                    <tr class="table-header text-center">
                        <td rowspan="2">DISCIPLINES</td>
                        <td rowspan="2">Coef</td>
                        <td rowspan="2">Moy / 20</td>
                        <td rowspan="2">Moy x Coef</td>
                        <td rowspan="2">Rang</td>
                        <td colspan="2">PROFESSEURS</td>
                    </tr>
                    <tr class="text-center">
                        <td colspan="1">NOM & PRENOMS</td>
                        <td colspan="1">APP/SIGNATURE</td>
                    </tr>
                    <?php foreach ($student->subject_by_module_groups as $subject_by_module_group) : ?>
                        <?php $sum_of_cofficient = 0; ?>
                        <?php $sum_of_avg_and_cofficient = 0; ?>
                        <?php $sum_of_subject = 0; ?>
                        <?php $sum_of_avg = 0; ?>
                        <?php $gloal_sum_of_avg = 0; ?>
                        <?php foreach ($subject_by_module_group["subjects"] as $result) : ?>
                            <?php if (!is_numeric($result["exam_group_exam_result_avg"]) || count($result["marks"]) == 0) {
                                continue;
                            } ?>
                            <tr>
                                <td><?= $result["subject_name"] ?></td>
                                <?php if (strtolower($result['subject_nature']) == "bonus") : ?>
                                    <?php $sum_of_avg += $result["exam_group_exam_result_bonus"]; ?>
                                    <?php $sum_of_avg_and_cofficient += $result["exam_group_exam_result_bonus"]; ?>
                                    <?php $gloal_sum_of_avg += $result["exam_group_exam_result_attendance"] ? 0 : $result["exam_group_exam_result_avg"]; ?>
                                    <td class="text-center"><b>Bonus</b></td>
                                    <td class="text-center"><?= $result["exam_group_exam_result_attendance"] ? "NC" : $result["exam_group_exam_result_avg"] ?></td>
                                    <td class="text-center"><?= $result["exam_group_exam_result_attendance"] ? "NC" : $result["exam_group_exam_result_bonus"] ?></td>
                                    <td class="text-center"><?= $result["exam_group_exam_result_attendance"] ? "NC" : $result["exam_group_exam_result_rang_str"] ?></td>
                                    <td><?= $result["teacher_name"] ?? "-" ?></td>
                                    <td><?= $result["exam_group_exam_result_attendance"] ? "NC" : $result["exam_group_exam_result_note"] ?></td>
                                <?php else : ?>
                                    <?php $sum_of_avg += $result["exam_group_exam_result_attendance"] ? 0 : $result["exam_group_exam_result_avg"]; ?>
                                    <?php $gloal_sum_of_avg += $result["exam_group_exam_result_attendance"] ? 0 : $result["exam_group_exam_result_avg"]; ?>
                                    <?php $sum_of_subject += $result["exam_group_exam_result_attendance"] ? 0 : 1 ?>
                                    <?php $sum_of_avg_and_cofficient += $result["exam_group_exam_result_attendance"] ? 0 : $result["exam_group_exam_result_avg"] * $result["coefficient"]; ?>
                                    <?php $sum_of_cofficient += $result["coefficient"]; ?>
                                    <td class="text-center"><?= $result["coefficient"] ?></td>
                                    <td class="text-center"><?= $result["exam_group_exam_result_attendance"] ? "NC" : $result["exam_group_exam_result_avg"] ?></td>
                                    <td class="text-center"><?= $result["exam_group_exam_result_attendance"] ? "NC" : $result["exam_group_exam_result_avg"] * $result["coefficient"] ?></td>
                                    <td class="text-center"><?= $result["exam_group_exam_result_attendance"] ? 'NC' : $result["exam_group_exam_result_rang_str"] ?></td>
                                    <td><?= $result["teacher_name"] ?? "-" ?></td>
                                    <td><?= $result["exam_group_exam_result_attendance"] ? "NC" : $result["exam_group_exam_result_note"] ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-header">
                            <td><?= $subject_by_module_group["module_name"] ?></td>
                            <td class="text-center"><?= $sum_of_cofficient ?></td>
                            <td class="text-center"><?= round($sum_of_avg_and_cofficient/$sum_of_cofficient,2) ?></td>
                            <td class="text-center"><?= $sum_of_avg_and_cofficient ?></td>
                            <!-- <td class="text-center">
                                <?= $student->exam_result_attendance ? "NC" : ($sum_of_avg > 1 && $sum_of_subject >= 1 ? round($sum_of_avg / $sum_of_subject, 2) : "") ?>
                            </td> -->
                            <td colspan="3"></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td style="border-right: 0"><b>TOTAUX</b></td>
                        <td class="text-center"><b><?= $student->exam_coef_sum ?></b></td>
                        <td class="text-center"><b><?= $student->exam_avg_total ?></b></td>
                        <td class="text-center" style="border-right: 0"><b><?= $student->exam_result_sum ?></b></td>
                        <td colspan="3" style="border-left: 0"></td>
                    </tr>
                    <tr class="table-header">
                        <td colspan="4" class="text-center">BILAN DE L'ELEVE</td>
                        <td colspan="3" class="text-center">BILAN DE LA CLASSE</td>
                    </tr>
                    <tr>
                        <td colspan="2" rowspan="5" class="text-center">
                            <h3>Heure d'absence</h3>
                            <table style="display: inline-block;">
                                <tr class="text-center">
                                    <td><b>Total</b></td>
                                    <td><b>Justifiée(s)</b></td>
                                    <td><b>Non justifiée(s)</b></td>
                                </tr>
                                <tr class="text-center">
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                        <td colspan="2" rowspan="5" class="text-center">
                            <h5>Résultat : <?= $exam_details->exam ?></h5>
                            <p>Moyenne: <?= $student->exam_result_attendance ? "NC" : ($student->exam_avg . " / 20") ?></p>
                            <p>Rang: <?= $student->exam_result_attendance ? "NC" : $student->exam_rang_str ?></p>
                        </td>
                    </tr>
                    <tr class="text-center table-header">
                        <td>MOY MAXI</td>
                        <td>MOY MINI</td>
                        <td>MOY CLASSE</td>
                    </tr>
                    <tr class="text-center">
                        <td><?= $stats["max_avg"] ?></td>
                        <td><?= $stats["min_avg"] ?></td>
                        <td><?= $stats["section_avg"] ?></td>
                    </tr>
                    <tr class="text-center table-header">
                        <td>0 - 8,49</td>
                        <td>8,50 - 9,99</td>
                        <td>10,00 - 20,00</td>
                    </tr>
                    <tr class="text-center">
                        <td><?= $stats["0_8"] ?></td>
                        <td><?= $stats["8_9"] ?></td>
                        <td><?= $stats["10_20"] ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center" style="vertical-align: middle;">
                            <b>Mention du conseil de classe</b>
                        </td>

                        <td colspan="2" rowspan="2" class="text-center" style="vertical-align: middle; background: #eee">
                            <?php if (isset($student->previous_exam_result) && !empty($student->previous_exam_result)) : ?>
                                <b style="text-decoration: underline;">Rappel</b><br /><br />
                                <?= $student->previous_exam_result['exam_title'] ?>: <b><?= $student->previous_exam_result['exam_avg'] ?></b><br />
                                Rang: <b><?= $student->previous_exam_result['exam_rang'] ?></b><br />
                            <?php endif; ?>
                        </td>
                        <td colspan="3" rowspan="10" class="text-center" style="vertical-align: middle;">
                            <?= $section["branch_city_name"] ?? $sch_setting->city ?>, le <?= date($sch_setting->date_format ?? 'd/m/Y') ?><br />
                            <span style="text-decoration: underline;">Nom et Prénoms, Cachet du Directeur des Etudes</span><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                            <b><?= $section["branch_responsible_name"] ?? $sch_setting->responsible ?></b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" rowspan="2" style="vertical-align: top;">
                            <b style="text-decoration: underline;">DISTINCTIONS</b>
                            <table style="margin-bottom: 2px">
                                <tr>
                                    <td class="text-center" style="border: 1px solid black; width: 18px; max-height: 4px; vertical-align: middle"><?= !$result["exam_group_exam_result_attendance"] && $student->exam_avg > 16 && $student->exam_avg <= 20 ? "X" : "" ?></td>
                                    <td style="border: none;">TH + Félicitations</td>
                                </tr>
                            </table>
                            <table style="margin-bottom: 2px">
                                <tr>
                                    <td class="text-center" style="border: 1px solid black; width: 18px; max-height: 4px; vertical-align: middle"><?= !$result["exam_group_exam_result_attendance"] && $student->exam_avg > 14 && $student->exam_avg <= 16 ? "X" : "" ?></td>
                                    <td style="border: none;">TH + Encouragements</td>
                                </tr>
                            </table>
                            <table style="margin-bottom: 2px">
                                <tr>
                                    <td class="text-center" style="border: 1px solid black; width: 18px; max-height: 4px; vertical-align: middle"><?= !$result["exam_group_exam_result_attendance"] && $student->exam_avg >= 12.5 && $student->exam_avg <= 14 ? "X" : "" ?></td>
                                    <td style="border: none;">TH</td>
                                </tr>
                            </table>
                            <br>
                            <b style="text-decoration: underline;">SANCTIONS</b>
                            <table>
                                <tr style="margin: 0; padding: 0px; border: 0px">
                                    <td style="margin: 0; padding: 0px; border: 0px">
                                        <table style="margin-bottom: 2px">
                                            <tr>
                                                <td class="text-center" style="border: 1px solid black; width: 18px; max-height: 4px; vertical-align: middle"></td>
                                                <td style="border: none;">AV Travail</td>
                                            </tr>
                                        </table>
                                        <table style="margin-bottom: 2px">
                                            <tr>
                                                <td class="text-center" style="border: 1px solid black; width: 18px; max-height: 4px; vertical-align: middle"></td>
                                                <td style="border: none;">BL Travail</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="margin: 0; padding: 0px; border: 0px">
                                        <table style="margin-bottom: 2px">
                                            <tr>
                                                <td class="text-center" style="border: 1px solid black; width: 18px; max-height: 4px; vertical-align: middle"></td>
                                                <td style="border: none;">AV Conduit</td>
                                            </tr>
                                        </table>
                                        <table style="margin-bottom: 2px">
                                            <tr>
                                                <td style="border: 1px solid black; width: 18px; height: 4px;"></td>
                                                <td style="border: none;">BL Conduit</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" rowspan="3" class="text-center" style="vertical-align: middle;">
                            <b style="text-decoration: underline;">Appréciation du conseil de classe</b>
                            <span><?= get_exam_note($student->exam_avg) ?></span><br><br>
                            <span style="text-decoration: underline;">Nom, signature du professeur principal</span><br>
                            <span><?= $section["principal_teacher_name"] ?? "" ?></span><br>
                            <br>
                            <br>
                            <br>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="bottom: 10px; position: absolute; width: 96%;">
                <table style="width: 100%" collapse="0">
                    <tr>
                        <td class="text-center" colspan="7" style="border: 1px solid black; height: 5px; padding: 3px">
                            <b><em>Conservez soigneusement ce bulletin de notes, aucun duplicata ne sera délivré.</em></b>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</body>

</html>