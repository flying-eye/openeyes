<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "pedigree".
 *
 * The followings are the available columns in table 'issue':
 *
 * @property int $id
 * @property int $inheritance_id
 * @property string $comments
 * @property int $consanguinity
 * @property int $gene_id
 * @property string $base_change
 * @property string $amino_acid_change
 * @property int $disorder_id
 * @property int $members
 * @property int $affecteds
 *
 * The followings are the available model relations:
 * @property PedigreeInheritance $inheritance
 * @property PedigreeGene $gene
 * @property Disorder $disorder
 */
class Pedigree extends BaseActiveRecord
{
    protected $lowest_version = 37;

    protected $highest_version = 38;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return Issue the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'pedigree';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'inheritance_id, comments, consanguinity, gene_id, base_change, amino_acid_change, disorder_id,' .
                'base_change_id, amino_acid_change_id, genomic_coordinate, genome_version, gene_transcript',
                'safe'
            ),
            array('consanguinity', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'inheritance' => array(self::BELONGS_TO, 'PedigreeInheritance', 'inheritance_id'),
            'gene' => array(self::BELONGS_TO, 'PedigreeGene', 'gene_id'),
            'disorder' => array(self::BELONGS_TO, 'Disorder', 'disorder_id'),
            'members' => array(self::HAS_MANY, 'GeneticsPatientPedigree', 'pedigree_id', 'with' => array('patient')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'Family ID',
            'inheritance_id' => 'Inheritance',
            'base_change' => 'Base change',
            'gene_id' => 'Gene',
            'amino_acid_change' => 'Amino acid change',
            'disorder_id' => 'Disorder',
            'base_change_id' => 'Base change type',
            'amino_acid_change_id' => 'Amino acid change type',
        );
    }

    /**
     * Get the possible versions for a genome
     *
     * @return array
     */
    public function genomeVersions()
    {
        return range($this->lowest_version, $this->highest_version);
    }
}
