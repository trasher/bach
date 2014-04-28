<?php
/**
 * Bach PMB Author entity
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurettes@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
namespace Bach\IndexationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Bach PMB Author entity
 *
 * @category Indexation
 * @package  Bach
 * @author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
 * @license  Unknown http://unknown.com
 * @link     http://anaphore.eu
 *
 * @ORM\Entity
 * @ORM\Table(name="PMBAuthor")
 */
class PMBAuthor
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",  length=20)
     */
    protected $type_auth;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true, length=30)
     */
    protected $function;

    /**
     * @ORM\ManyToOne(targetEntity="PMBFileFormat", inversedBy="authors")
     * @ORM\JoinColumn(name="pmbfile_id", referencedColumnName="uniqid")
     */
    protected $pmbfile;

    /**
     * Main constructor
     *
     * @param string        $type     Entity type
     * @param string        $name     Entity name firstname
     * @param string        $function Entity code function
     * @param PMBFileFormat $pmb      Entity pmbfileformat
     */
    public function __construct($type, $name, $function, $pmb)
    {
        $this->pmbfile = $pmb;
        $this->type_auth = $type;
        $this->name = $name;
        $this->function = self::convertCodeFunction($function);
    }

    /**
     * Parse converter function
     *
     * @param string $code code function
     *
     * @return string
     */
    public static function convertCodeFunction($code)
    {
        $value=null;
        switch ($code) {
        case '005':
            $value = _('Actor');
            break;
        case '010':
            $value = _('Adaptator');
            break;
        case '018':
            $value = _('Author of the animation');
            break;
        case '020':
            $value = _('Annotator');
            break;
        case '030':
            $value = _('Arranger');
            break;
        case '040':
            $value = _('Artist');
            break;
        case '050':
            $value = _('Rightholder');
            break;
        case '060':
            $value = _('Aassociated name');
            break;
        case '065':
            $value = _('Auctioneer');
            break;
        case '070':
            $value = _('Author');
            break;
        case '072':
            $value = _('Author of a quote or extracts');
            break;
        case '075':
            $value = _('Postfacier, author of the colophon, etc.');
            break;
        case '080':
            $value = _('Prefacer, etc.');
            break;
        case '090':
            $value = _('Dialogist');
            break;
        case '100':
            $value = _('Bibliographic antecedent');
            break;
        case '110':
            $value = _('Bookbinder');
            break;
        case '120':
            $value = _('Model maker of the bookbinding');
            break;
        case '130':
            $value = _('Model maker');
            break;
        case '140':
            $value = _('Designer cover');
            break;
        case '150':
            $value = _('Designer bookplate');
            break;
        case '160':
            $value = _('Bookseller');
            break;
        case '170':
            $value = _('Calligrapher');
            break;
        case '180':
            $value = _('Mapmaker');
            break;
        case '190':
            $value = _('Censor');
            break;
        case '195':
            $value = _('Choir conductor');
            break;
        case '200':
            $value = _('Action Director');
            break;
        case '202':
            $value = _('Circus Performer');
            break;
        case '205':
            $value = _('Contributor');
            break;
        case '207':
            $value = _('Humorist');
            break;
        case '210':
            $value = _('Commentator');
            break;
        case '212':
            $value = _('Comment author');
            break;
        case '220':
            $value = _('Compiler');
            break;
        case '230':
            $value = _('Compositor');
            break;
        case '240':
            $value = _('Compositor printing works');
            break;
        case '245':
            $value = _('Designer');
            break;
        case '250':
            $value = _('Conductor');
            break;
        case '255':
            $value = _('Project consulting');
            break;
        case '257':
            $value = _('Continuator');
            break;
        case '260':
            $value = _('Copyright holder');
            break;
        case '270':
            $value = _('Proofreader');
            break;
        case '273':
            $value = _('Exhibition curator');
            break;
        case '274':
            $value = _('Danser');
            break;
        case '280':
            $value = _('Dedicatee');
            break;
        case '290':
            $value = _('Dedicator');
            break;
        case '295':
            $value = _('Organization  of viva');
            break;
        case '300':
            $value = _('Director');
            break;
        case '305':
            $value = _('Applicant');
            break;
        case '310':
            $value = _('Distributor');
            break;
        case '320':
            $value = _('Patron');
            break;
        case '330':
            $value = _('Alleged author');
            break;
        case '340':
            $value = _('Scientific Editor');
            break;
        case '350':
            $value = _('Engraver');
            break;
        case '360':
            $value = _('Etcher');
            break;
        case '365':
            $value = _('Expert');
            break;
        case '370':
            $value = _('film editor');
            break;
        case '380':
            $value = _('Counterfeiter');
            break;
        case '390':
            $value = _('Former owner');
            break;
        case '395':
            $value = _('Founder');
            break;
        case '400':
            $value = _('Patron');
            break;
        case '410':
            $value = _('Graphic technicians');
            break;
        case '420':
            $value = _('Honoured person');
            break;
        case '430':
            $value = _('Illuminator');
            break;
        case '440':
            $value = _('Illustrator');
            break;
        case '450':
            $value = _('Sending person');
            break;
        case '460':
            $value = _('Interviewee');
            break;
        case '470':
            $value = _('Interviewer');
            break;
        case '475':
            $value = _('Publisher community');
            break;
        case '480':
            $value = _('Librettist');
            break;
        case '490':
            $value = _('Licence holder');
            break;
        case '500':
            $value = _('Licensor');
            break;
        case '510':
            $value = _('Lithographer');
            break;
        case '520':
            $value = _('Lyricist');
            break;
        case '530':
            $value = _('Engraver metal');
            break;
        case '535':
            $value = _('Mime artist');
            break;
        case '540':
            $value = _('Monitor');
            break;
        case '545':
            $value = _('Musician');
            break;
        case '550':
            $value = _('Narrator');
            break;
        case '555':
            $value = _('Opposent');
            break;
        case '557':
            $value = _('Meeting planner');
            break;
        case '560':
            $value = _('Instigator');
            break;
        case '570':
            $value = _('Other');
            break;
        case '580':
            $value = _('Manufacturer of the paper');
            break;
        case '582':
            $value = _('Patent applicant');
            break;
        case '584':
            $value = _('Inventor of the patent');
            break;
        case '587':
            $value = _('Patent holder');
            break;
        case '590':
            $value = _('Performer');
            break;
        case '595':
            $value = _('Research Manager');
            break;
        case '600':
            $value = _('Photographer');
            break;
        case '605':
            $value = _('Presenter');
            break;
        case '610':
            $value = _('Printer');
            break;
        case '620':
            $value = _('Printer engravings');
            break;
        case '630':
            $value = _('Producer');
            break;
        case '632':
            $value = _('Artistic director');
            break;
        case '633':
            $value = _('Member of the production crew');
            break;
        case '635':
            $value = _('Programmer');
            break;
        case '637':
            $value = _('Project managers');
            break;
        case '640':
            $value = _('Proofreading');
            break;
        case '650':
            $value = _('Commercial editor');
            break;
        case '651':
            $value = _('Publication manager');
            break;
        case '655':
            $value = _('Puppeteer');
            break;
        case '660':
            $value = _('Recipient of letters');
            break;
        case '670':
            $value = _('sound engineer');
            break;
        case '673':
            $value = _('Manager of the research team');
            break;
        case '675':
            $value = _('Head of critical review');
            break;
        case '677':
            $value = _('Member of the research team');
            break;
        case '680':
            $value = _('Rubricateur');
            break;
        case '690':
            $value = _('Scriptwriter');
            break;
        case '695':
            $value = _('Scientific adviser');
            break;
        case '700':
            $value = _('Scrivener');
            break;
        case '705':
            $value = _('Sculptor');
            break;
        case '710':
            $value = _('Secretary');
            break;
        case '720':
            $value = _('Signatory');
            break;
        case '721':
            $value = _('Singer');
            break;
        case '723':
            $value = _('Sponsor');
            break;
        case '725':
            $value = _('Organisation of standardisation');
            break;
        case '726':
            $value = _('Stuntman');
            break;
        case '727':
            $value = _('Thesis advisor');
            break;
        case '730':
            $value = _('Translator');
            break;
        case '740':
            $value = _('Designer typeface');
            break;
        case '750':
            $value = _('typographer');
            break;
        case '753':
            $value = _('Seller');
            break;
        case '755':
            $value = _('Vocalist');
            break;
        case '760':
            $value = _('Woodcutter');
            break;
        case '770':
            $value = _('Accompanying material author');
            break;
        default:
            $value=$code;
            break;
        }
        return $value;
    }
    /**
     * Get uniqid
     *
     * @return integer
     */
    public function getUniqid()
    {
        return $this->uniqid;
    }

    /**
     * Set author type
     *
     * @param string $type_auth Author type
     *
     * @return PMBAutor
     */
    public function setTypeAuth($type_auth)
    {
        $this->type_auth = $type_auth;
        return $this;
    }

    /**
     * Get author type
     *
     * @return string
     */
    public function getTypeAuth()
    {
        return $this->type_auth;
    }

    /**
     * Set name
     *
     * @param string $name Last name
     *
     * @return PMBAuthor
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstname
     *
     * @param string $firstname First name
     *
     * @return PMBAuthor
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set function
     *
     * @param string $function function code
     *
     * @return PMBAuthor
     */
    public function setFunction($function)
    {
        $this->function = $function;
        return $this;
    }

    /**
     * Get codefonction
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set PMB file
     *
     * @param PMBFileFormat $pmbfile PMB file
     *
     * @return PMBAuthor
     */
    public function setPmbfile(PMBFileFormat $pmbfile)
    {
        $this->pmbfile = $pmbfile;
        return $this;
    }

    /**
     * Get PMB file
     *
     * @return PMBFileFormat
     */
    public function getPmbfile()
    {
        return $this->pmbfile;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
