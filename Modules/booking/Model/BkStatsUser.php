<?php

require_once 'Framework/Model.php';
require_once 'Modules/booking/Model/BkAuthorization.php';
require_once 'Modules/core/Model/CoreTranslator.php';

require_once 'Modules/resources/Model/ReCategory.php';
require_once 'Modules/resources/Model/ResourceInfo.php';

require_once 'Modules/clients/Model/ClClient.php';

/**
 * Class defining methods for statistics calculation for users
 *
 * @author Sylvain Prigent
 */
class BkStatsUser extends Model {

    public function authorizedUsersMail($resource_id, $id_space) {
        include_once ("externals/PHPExcel/Classes/PHPExcel.php");
        include_once ("externals/PHPExcel/Classes/PHPExcel/Writer/Excel5.php");
        include_once ("externals/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

        // get resource category
        $modelResource = new ReCategory();
        $resourceInfo = $modelResource->getName($resource_id);

        // header
        $today = date('d/m/Y');
        $equipement = $resourceInfo;
        $header = "Date d'édition de ce document : \n" . $today;
        $titre = "Liste des utilisateurs formés sur l'équipement : " . $equipement;
        $avertissement = "L'utilisation de cet équipement nécessite un accord et/ou une formation par le personnel de la plateforme";
        $reservation = "La réservation de cet équipement, par les utilisateurs formés, est possible via l'agenda " . Configuration::get("name");

        // file name
        $id = $resource_id;
        $nom = date('Y-m-d-H-i') . "_" . $id . ".xlsx";
        $teamName = Configuration::get("name");
        $footer = "" . $teamName . "/exportFiles/" . $nom;


        $modelAuthorisation = new BkAuthorization();
        $res = $modelAuthorisation->getActiveAuthorizationSummaryForResourceCategory($resource_id, "");

        //$q = array('equipement'=>$equipement);
        //$sql = 'SELECT DISTINCT nf, laboratoire, date_unix, visa FROM autorisation WHERE machine=:equipement ORDER by nf';
        //$req = $cnx->prepare($sql);
        //$req->execute($q);
        //$res = $req->fetchAll();
        // Création de l'objet
        $objPHPExcel = new PHPExcel();

        // Définition de quelques propriétés
        $objPHPExcel->getProperties()->setCreator($teamName);
        $objPHPExcel->getProperties()->setLastModifiedBy($teamName);
        $objPHPExcel->getProperties()->setTitle("Liste d'utilisateurs autorises");
        $objPHPExcel->getProperties()->setSubject("Equipement = " . $equipement);
        $objPHPExcel->getProperties()->setDescription("Fichier genere avec PHPExel depuis la base de donnees");


        $center = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
        $gras = array('font' => array('bold' => true));
        $border = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN)));
        $borderLR = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE)));

        $borderG = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM)));

        $borderLRB = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN)));

        $style = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '000000'),
                'size' => 15,
                'name' => 'Calibri'
        ));

        $style2 = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '000000'),
                'size' => 10,
                'name' => 'Calibri'
        ));

        // Nommage de la feuille
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Liste utilisateurs');

        // Mise en page de la feuille
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $sheet->setBreak('A55', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E55', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A110', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E110', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A165', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E165', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A220', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E220', PHPExcel_Worksheet::BREAK_COLUMN);
        //$sheet->getPageSetup()->setFitToWidth(1);
        //$sheet->getPageSetup()->setFitToHeight(10);
        $sheet->getPageMargins()->SetTop(0.9);
        $sheet->getPageMargins()->SetBottom(0.5);
        $sheet->getPageMargins()->SetLeft(0.2);
        $sheet->getPageMargins()->SetRight(0.2);
        $sheet->getPageMargins()->SetHeader(0.2);
        $sheet->getPageMargins()->SetFooter(0.2);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        //$sheet->getPageSetup()->setVerticalCentered(false);

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(40);

        // Header
        $sqlIcon = "SELECT image FROM core_spaces WHERE id=?";
        $reqIcon = $this->runRequest($sqlIcon, array($id_space))->fetch();
        $objDrawing = new PHPExcel_Worksheet_HeaderFooterDrawing();
        $objDrawing->setName('PHPExcel logo');
        $objDrawing->setPath($reqIcon[0]);
        $objDrawing->setHeight(60);
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);
        $sheet->getHeaderFooter()->setOddHeader('&L&G&R' . $header);

        // Titre
        $ligne = 1;
        $colonne = 'A';
        $sheet->getRowDimension($ligne)->setRowHeight(30);
        $sheet->SetCellValue($colonne . $ligne, $titre);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($style);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($gras);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
        $sheet->getStyle($colonne . $ligne)->getAlignment()->setWrapText(true);
        $sheet->mergeCells($colonne . $ligne . ':D' . $ligne);

        // Avertissement
        $ligne = 2;
        $sheet->mergeCells('A' . $ligne . ':D' . $ligne);
        $sheet->SetCellValue('A' . $ligne, $avertissement);
        $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
        $sheet->getStyle('A' . $ligne)->applyFromArray($center);
        $sheet->getStyle('A' . $ligne)->getAlignment()->setWrapText(true);

        // Réservation
        $ligne = 3;
        $sheet->mergeCells('A' . $ligne . ':D' . $ligne);
        $sheet->SetCellValue('A' . $ligne, $reservation);
        $sheet->getStyle('A' . $ligne)->applyFromArray($center);


        $ligne = 5;
        $sheet->SetCellValue('A' . $ligne, "NOM Prénom");
        $sheet->getStyle('A' . $ligne)->applyFromArray($border);
        $sheet->getStyle('A' . $ligne)->applyFromArray($center);
        $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('B' . $ligne, "Laboratoire");
        $sheet->getStyle('B' . $ligne)->applyFromArray($border);
        $sheet->getStyle('B' . $ligne)->applyFromArray($center);
        $sheet->getStyle('B' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('C' . $ligne, "Couriel");
        $sheet->getStyle('C' . $ligne)->applyFromArray($border);
        $sheet->getStyle('C' . $ligne)->applyFromArray($center);
        $sheet->getStyle('C' . $ligne)->applyFromArray($gras);


        $ligne = 6;
        foreach ($res as $r) {
            //print_r($r);
            $colonne = 'A';
            $sheet->getRowDimension($ligne)->setRowHeight(13);

            $sheet->SetCellValue($colonne . $ligne, $r["name"] . " " . $r["firstname"]); // user name
            $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
            $colonne++;
            $sheet->SetCellValue($colonne . $ligne, $r["unitName"]); // unit name
            $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
            $colonne++;
            $sheet->SetCellValue($colonne . $ligne, $r["email"]); // visa name
            $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);

            if (!($ligne % 55)) {
                $sheet->getStyle('A' . $ligne)->applyFromArray($borderLRB);
                $sheet->getStyle('B' . $ligne)->applyFromArray($borderLRB);
                $sheet->getStyle('C' . $ligne)->applyFromArray($borderLRB);
                $ligne++;
                // Titre
                $colonne = 'A';
                $sheet->getRowDimension($ligne)->setRowHeight(30);
                $sheet->SetCellValue($colonne . $ligne, $titre);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($gras);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->getAlignment()->setWrapText(true);
                $sheet->mergeCells($colonne . $ligne . ':D' . $ligne);

                // Avertissement
                $ligne++;
                $sheet->mergeCells('A' . $ligne . ':D' . $ligne);
                $sheet->SetCellValue('A' . $ligne, $avertissement);
                $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
                $sheet->getStyle('A' . $ligne)->applyFromArray($center);
                $sheet->getStyle('A' . $ligne)->getAlignment()->setWrapText(true);

                // Réservation
                $ligne++;
                $sheet->mergeCells('A' . $ligne . ':D' . $ligne);
                $sheet->SetCellValue('A' . $ligne, $reservation);
                $sheet->getStyle('A' . $ligne)->applyFromArray($center);

                // Noms des colonnes
                $ligne+=2;
                $sheet->SetCellValue('A' . $ligne, "NOM Prénom");
                $sheet->getStyle('A' . $ligne)->applyFromArray($border);
                $sheet->getStyle('A' . $ligne)->applyFromArray($center);
                $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
                $sheet->SetCellValue('B' . $ligne, "Laboratoire");
                $sheet->getStyle('B' . $ligne)->applyFromArray($border);
                $sheet->getStyle('B' . $ligne)->applyFromArray($center);
                $sheet->getStyle('B' . $ligne)->applyFromArray($gras);
                $sheet->SetCellValue('C' . $ligne, "Couriel");
                $sheet->getStyle('C' . $ligne)->applyFromArray($border);
                $sheet->getStyle('C' . $ligne)->applyFromArray($center);
                $sheet->getStyle('C' . $ligne)->applyFromArray($gras);
            }
            $ligne++;
        }
        $ligne--;
        $sheet->getStyle('A' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('B' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('C' . $ligne)->applyFromArray($borderLRB);

        // Footer
        $sheet->getHeaderFooter()->setOddFooter('&L ' . $footer . '&R Page &P / &N');
        $sheet->getHeaderFooter()->setEvenFooter('&L ' . $footer . '&R Page &P / &N');

        $ImageNews = $reqIcon[0];

        //on récupère l'extension du fichier
        $ExtensionPresumee = explode('.', $ImageNews);
        $ExtensionPresumee = strtolower($ExtensionPresumee[count($ExtensionPresumee) - 1]);
        //on utilise la fonction php associé au bon type d'image
        if ($ExtensionPresumee == 'jpg' || $ExtensionPresumee == 'jpeg') {
            $ImageChoisie = imagecreatefromjpeg($ImageNews);
        } elseif ($ExtensionPresumee == 'gif') {
            $ImageChoisie = imagecreatefromgif($ImageNews);
        } elseif ($ExtensionPresumee == 'png') {
            $ImageChoisie = imagecreatefrompng($ImageNews);
        }

        //je redimensionne l’image
        $TailleImageChoisie = getimagesize($ImageNews);
        //la largeur voulu dans le document excel
        //$NouvelleLargeur = 150;
        $NouvelleHauteur = 80;
        //calcul du pourcentage de réduction par rapport à l’original
        //$Reduction = ( ($NouvelleLargeur * 100)/$TailleImageChoisie[0] );
        $Reduction = ( ($NouvelleHauteur * 100) / $TailleImageChoisie[1] );
        //PHPExcel m’aplatit verticalement l’image donc j’ai calculé de ratio d’applatissement de l’image et je l’étend préalablement
        //$NouvelleHauteur = (($TailleImageChoisie[1] * $Reduction)/100 );
        $NouvelleLargeur = (($TailleImageChoisie[0] * $Reduction) / 100 );
        //j’initialise la nouvelle image
        $NouvelleImage = imagecreatetruecolor($NouvelleLargeur, $NouvelleHauteur) or die(“Erreur”);

        //je mets l’image obtenue après redimensionnement en variable
        imagecopyresampled($NouvelleImage, $ImageChoisie, 0, 0, 0, 0, $NouvelleLargeur, $NouvelleHauteur, $TailleImageChoisie[0], $TailleImageChoisie[1]);
        $gdImage = $NouvelleImage;

        //on créé l’objet de dessin et on lui donne un nom, l’image, la position de l’image, la compression de l’image, le type mime…
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample image');
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setOffsetX(50);
        $objDrawing->setOffsetY(8);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        //enfin on l’envoie à la feuille de calcul !
        //$objDrawing->setWorksheet($sheet);


        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $writer->save('./data/' . $nom);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $nom . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    /**
     * Statistics of the users allowed to book a resource
     * @param number $resource_id
     */
    public function authorizedUsers($resource_id, $id_space, $lang) {
        
        include_once ("externals/PHPExcel/Classes/PHPExcel.php");
        include_once ("externals/PHPExcel/Classes/PHPExcel/Writer/Excel5.php");
        include_once ("externals/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

        // get resource category
        $modelResource = new ReCategory();
        $resourceInfo = $modelResource->getName($resource_id); // ->getResourcesCategory($resource_id);
        // header
        $today = date('d/m/Y');
        $equipement = $resourceInfo;
        $header = "Date d'édition de ce document : \n" . $today;
        $titre = "Liste des utilisateurs formés sur l'équipement : " . $equipement;
        $avertissement = "L'utilisation de cet équipement nécessite un accord et/ou une formation par le personnel de la plateforme";
        $reservation = "La réservation de cet équipement, par les utilisateurs formés, est possible via l'agenda " . Configuration::get("name");

        // file name
        $id = $resource_id;
        $nom = date('Y-m-d-H-i') . "_" . $id . ".xlsx";
        $teamName = Configuration::get("name");
        $footer = "platform-manager/" . $teamName . "/exportFiles/" . $nom;

        $modelAuthorisation = new BkAuthorization();
        $res = $modelAuthorisation->getActiveAuthorizationSummaryForResourceCategory($resource_id, $lang);

        // Création de l'objet
        $objPHPExcel = new PHPExcel();

        // Définition de quelques propriétés
        $objPHPExcel->getProperties()->setCreator($teamName);
        $objPHPExcel->getProperties()->setLastModifiedBy($teamName);
        $objPHPExcel->getProperties()->setTitle("Liste d'utilisateurs autorises");
        $objPHPExcel->getProperties()->setSubject("Equipement = " . $equipement);
        $objPHPExcel->getProperties()->setDescription("Fichier genere avec PHPExel depuis la base de donnees");

        $center = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
        $gras = array('font' => array('bold' => true));
        $border = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN)));
        $borderLR = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE)));

        $borderG = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM)));

        $borderLRB = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN)));

        $style = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '000000'),
                'size' => 15,
                'name' => 'Calibri'
        ));

        $style2 = array(
            'font' => array(
                'bold' => false,
                'color' => array('rgb' => '000000'),
                'size' => 10,
                'name' => 'Calibri'
        ));

        // Nommage de la feuille
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Liste utilisateurs');

        // Mise en page de la feuille
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $sheet->setBreak('A55', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E55', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A110', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E110', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A165', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E165', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A220', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E220', PHPExcel_Worksheet::BREAK_COLUMN);
        //$sheet->getPageSetup()->setFitToWidth(1);
        //$sheet->getPageSetup()->setFitToHeight(10);
        $sheet->getPageMargins()->SetTop(0.9);
        $sheet->getPageMargins()->SetBottom(0.5);
        $sheet->getPageMargins()->SetLeft(0.2);
        $sheet->getPageMargins()->SetRight(0.2);
        $sheet->getPageMargins()->SetHeader(0.2);
        $sheet->getPageMargins()->SetFooter(0.2);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        //$sheet->getPageSetup()->setVerticalCentered(false);

        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(8);

        // Header
        $objDrawing = new PHPExcel_Worksheet_HeaderFooterDrawing();
        $objDrawing->setName('PHPExcel logo');

        $sqlIcon = "SELECT image FROM core_spaces WHERE id=?";
        $reqIcon = $this->runRequest($sqlIcon, array($id_space))->fetch();

        //echo "icon = " . $reqIcon[0] . "<br/>";
        $objDrawing->setPath($reqIcon[0]);
        $objDrawing->setHeight(60);
        $objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);
        $sheet->getHeaderFooter()->setOddHeader('&L&G&R' . $header);


        // Titre
        $ligne = 1;
        $colonne = 'A';
        $sheet->getRowDimension($ligne)->setRowHeight(30);
        $sheet->SetCellValue($colonne . $ligne, $titre);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($style);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($gras);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
        $sheet->getStyle($colonne . $ligne)->getAlignment()->setWrapText(true);
        $sheet->mergeCells($colonne . $ligne . ':D' . $ligne);

        // Avertissement
        $ligne = 2;
        $sheet->mergeCells('A' . $ligne . ':D' . $ligne);
        $sheet->SetCellValue('A' . $ligne, $avertissement);
        $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
        $sheet->getStyle('A' . $ligne)->applyFromArray($center);
        $sheet->getStyle('A' . $ligne)->getAlignment()->setWrapText(true);

        // Réservation
        $ligne = 3;
        $sheet->mergeCells('A' . $ligne . ':D' . $ligne);
        $sheet->SetCellValue('A' . $ligne, $reservation);
        $sheet->getStyle('A' . $ligne)->applyFromArray($center);


        $ligne = 5;
        $sheet->SetCellValue('A' . $ligne, "NOM Prénom");
        $sheet->getStyle('A' . $ligne)->applyFromArray($border);
        $sheet->getStyle('A' . $ligne)->applyFromArray($center);
        $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('B' . $ligne, "Laboratoire");
        $sheet->getStyle('B' . $ligne)->applyFromArray($border);
        $sheet->getStyle('B' . $ligne)->applyFromArray($center);
        $sheet->getStyle('B' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('C' . $ligne, "Date");
        $sheet->getStyle('C' . $ligne)->applyFromArray($border);
        $sheet->getStyle('C' . $ligne)->applyFromArray($center);
        $sheet->getStyle('C' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('D' . $ligne, "VISA");
        $sheet->getStyle('D' . $ligne)->applyFromArray($border);
        $sheet->getStyle('D' . $ligne)->applyFromArray($center);
        $sheet->getStyle('D' . $ligne)->applyFromArray($gras);


        $ligne = 6;
        foreach ($res as $r) {
            $colonne = 'A';
            $sheet->getRowDimension($ligne)->setRowHeight(13);

            $sheet->SetCellValue($colonne . $ligne, $r["name"] . " " . $r["firstname"]); // user name
            $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
            $colonne++;
            $sheet->SetCellValue($colonne . $ligne, $r["unitName"]); // unit name
            $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
            $colonne++;
            //$date=date('d/m/Y', $r[2]); // date
            $sheet->SetCellValue($colonne . $ligne, CoreTranslator::dateFromEn($r["date"], $lang));
            $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
            $colonne++;
            $sheet->SetCellValue($colonne . $ligne, $r["visa"]); // visa name
            $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
            $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);

            if (!($ligne % 55)) {
                $sheet->getStyle('A' . $ligne)->applyFromArray($borderLRB);
                $sheet->getStyle('B' . $ligne)->applyFromArray($borderLRB);
                $sheet->getStyle('C' . $ligne)->applyFromArray($borderLRB);
                $sheet->getStyle('D' . $ligne)->applyFromArray($borderLRB);
                $ligne++;
                // Titre
                $colonne = 'A';
                $sheet->getRowDimension($ligne)->setRowHeight(30);
                $sheet->SetCellValue($colonne . $ligne, $titre);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($gras);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->getAlignment()->setWrapText(true);
                $sheet->mergeCells($colonne . $ligne . ':D' . $ligne);

                // Avertissement
                $ligne++;
                $sheet->mergeCells('A' . $ligne . ':D' . $ligne);
                $sheet->SetCellValue('A' . $ligne, $avertissement);
                $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
                $sheet->getStyle('A' . $ligne)->applyFromArray($center);
                $sheet->getStyle('A' . $ligne)->getAlignment()->setWrapText(true);

                // Réservation
                $ligne++;
                $sheet->mergeCells('A' . $ligne . ':D' . $ligne);
                $sheet->SetCellValue('A' . $ligne, $reservation);
                $sheet->getStyle('A' . $ligne)->applyFromArray($center);

                // Noms des colonnes
                $ligne+=2;
                $sheet->SetCellValue('A' . $ligne, "NOM Prénom");
                $sheet->getStyle('A' . $ligne)->applyFromArray($border);
                $sheet->getStyle('A' . $ligne)->applyFromArray($center);
                $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
                $sheet->SetCellValue('B' . $ligne, "Laboratoire");
                $sheet->getStyle('B' . $ligne)->applyFromArray($border);
                $sheet->getStyle('B' . $ligne)->applyFromArray($center);
                $sheet->getStyle('B' . $ligne)->applyFromArray($gras);
                $sheet->SetCellValue('C' . $ligne, "Date");
                $sheet->getStyle('C' . $ligne)->applyFromArray($border);
                $sheet->getStyle('C' . $ligne)->applyFromArray($center);
                $sheet->getStyle('C' . $ligne)->applyFromArray($gras);
                $sheet->SetCellValue('D' . $ligne, "VISA");
                $sheet->getStyle('D' . $ligne)->applyFromArray($border);
                $sheet->getStyle('D' . $ligne)->applyFromArray($center);
                $sheet->getStyle('D' . $ligne)->applyFromArray($gras);
            }
            $ligne++;
        }
        $ligne--;
        $sheet->getStyle('A' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('B' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('C' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('D' . $ligne)->applyFromArray($borderLRB);

        // Footer
        $sheet->getHeaderFooter()->setOddFooter('&L ' . $footer . '&R Page &P / &N');
        $sheet->getHeaderFooter()->setEvenFooter('&L ' . $footer . '&R Page &P / &N');

        $ImageNews = $reqIcon[0];

        //on récupère l'extension du fichier
        $ExtensionPresumee = explode('.', $ImageNews);
        $ExtensionPresumee = strtolower($ExtensionPresumee[count($ExtensionPresumee) - 1]);
        //on utilise la fonction php associé au bon type d'image
        if ($ExtensionPresumee == 'jpg' || $ExtensionPresumee == 'jpeg') {
            $ImageChoisie = imagecreatefromjpeg($ImageNews);
        } elseif ($ExtensionPresumee == 'gif') {
            $ImageChoisie = imagecreatefromgif($ImageNews);
        } elseif ($ExtensionPresumee == 'png') {
            $ImageChoisie = imagecreatefrompng($ImageNews);
        }

        //je redimensionne l’image
        $TailleImageChoisie = getimagesize($ImageNews);
        //la largeur voulu dans le document excel
        //$NouvelleLargeur = 150;
        $NouvelleHauteur = 80;
        //calcul du pourcentage de réduction par rapport à l’original
        //$Reduction = ( ($NouvelleLargeur * 100)/$TailleImageChoisie[0] );
        $Reduction = ( ($NouvelleHauteur * 100) / $TailleImageChoisie[1] );
        //PHPExcel m’aplatit verticalement l’image donc j’ai calculé de ratio d’applatissement de l’image et je l’étend préalablement
        //$NouvelleHauteur = (($TailleImageChoisie[1] * $Reduction)/100 );
        $NouvelleLargeur = (($TailleImageChoisie[0] * $Reduction) / 100 );
        //j’initialise la nouvelle image
        $NouvelleImage = imagecreatetruecolor($NouvelleLargeur, $NouvelleHauteur) or die(“Erreur”);

        //je mets l’image obtenue après redimensionnement en variable
        imagecopyresampled($NouvelleImage, $ImageChoisie, 0, 0, 0, 0, $NouvelleLargeur, $NouvelleHauteur, $TailleImageChoisie[0], $TailleImageChoisie[1]);
        $gdImage = $NouvelleImage;

        //on créé l’objet de dessin et on lui donne un nom, l’image, la position de l’image, la compression de l’image, le type mime…
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample image');
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setCoordinates('A1');
        $objDrawing->setOffsetX(50);
        $objDrawing->setOffsetY(8);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        
        //$objDrawing->setWorksheet($sheet);


        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        $writer->save('./data/' . $nom);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $nom . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    public function bookingUsers($id_space, $startdate, $enddate) {

        // convert start date to unix date
        $tabDate = explode("-", $startdate);
        $date_debut = $tabDate[2] . '/' . $tabDate[1] . '/' . $tabDate[0];
        $searchDate_start = mktime(0, 0, 0, $tabDate[1], $tabDate[2], $tabDate[0]);

        // convert end date to unix date
        $tabDate = explode("-", $enddate);
        $date_fin = $tabDate[2] . '/' . $tabDate[1] . '/' . $tabDate[0];
        $searchDate_end = mktime(0, 0, 0, $tabDate[1], $tabDate[2] + 1, $tabDate[0]);

        //  get all the booking users
        $q = array('start' => $searchDate_start, 'end' => $searchDate_end, 'space' => $id_space);
        $sql = 'SELECT DISTINCT recipient_id FROM bk_calendar_entry WHERE
				(start_time >=:start AND start_time <= :end)
                                AND resource_id IN (SELECT id FROM re_info WHERE id_space=:space)';
        $req = $this->runRequest($sql, $q);
        $recs = $req->fetchAll();

        $sql2 = 'SELECT DISTINCT responsible_id FROM bk_calendar_entry WHERE
				(start_time >=:start AND start_time <= :end)
                                AND resource_id IN (SELECT id FROM re_info WHERE id_space=:space)';
        $req2 = $this->runRequest($sql2, $q);
        $recresps = $req2->fetchAll();

        // get the users informations (name, firstname, unit, email)
        $modelUser = new CoreUser();
        $modelClient = new ClClient();
        $recss = array();
        for ($i = 0; $i < count($recs); $i++) {

            $recss[] = array('name' => $modelUser->getUserFUllName($recs[$i]['recipient_id']),
                'email' => $modelUser->getEmail($recs[$i]['recipient_id']));
        }
        for ($i = 0; $i < count($recresps); $i++) {
            $clientInfo = $modelClient->get($recresps[$i]['responsible_id']);
            $recss[] = array(
                'name' => $clientInfo["contact_name"],
                'email' => $clientInfo["email"] 
            );
        }

        return $recss;
    }

}
