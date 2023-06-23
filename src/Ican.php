<?php

class Ican {
    public $grade, $overallCategory;

    public function __construct($grade, $overallCategory) {
        $this->grade = $grade;
        $this->overallCategory = $overallCategory;
    }

    public function getGrade() {
        return $this->grade;
    }

    public function getOverallCategory() {
        return $this->overallCategory;
    }

    public function setGrade($grade) {
        $this->grade = $grade;
    }

    public function setOverallCategory($overallCategory) {
        $this->overallCategory = $overallCategory;
    }

    // query the database for the statements based on the grade and overall category
    public function getStatements() {
        $db = new Db();
        $q = 'SELECT `statement` FROM `i_can` WHERE `grade` = :grade AND `overall_category` = :overall_category';
        $data = array(
            'grade' => $this->grade,
            'overall_category' => $this->overallCategory
        );
        
        $data = $db->prepExecFetchAll($q, $data);

        $returnStatements = '';

        foreach($data as $item){
            $returnStatements .= $item['statement']."\n";
        }
        return $returnStatements;
    }
}