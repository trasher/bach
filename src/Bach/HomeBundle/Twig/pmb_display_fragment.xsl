<?xml version="1.0" encoding="UTF-8"?>
<!--

Displays an PMB fragment as HTML

Copyright (c) 2014, Anaphore
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are
met:

    (1) Redistributions of source code must retain the above copyright
    notice, this list of conditions and the following disclaimer.

    (2) Redistributions in binary form must reproduce the above copyright
    notice, this list of conditions and the following disclaimer in
    the documentation and/or other materials provided with the
    distribution.

    (3)The name of the author may not be used to
   endorse or promote products derived from this software without
   specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.

@author   Vincent Fleurette <vincent.fleurette@anaphore.eu>
@license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
@link     http://anaphore.eu
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl"
    exclude-result-prefixes="php">

    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:template match="notice">
        <header>
            <h2 property="dc:title">
                <xsl:value-of select="//titrePrincipal"/>
            </h2>
        </header>
        <xsl:apply-templates/>
    </xsl:template>
    
    <xsl:template match="zoneTitre">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="titreComplement|titrePropreAuteurDiffzoneTitre|titreParallele">
        <p>
            <xsl:choose>
                <xsl:when test="local-name() = 'titreComplement'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Titre complement:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'titrePropreAuteurDiffzoneTitre'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Titre parallel:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'titreParallele'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Title proper author different:')"/>
                    </strong>
                </xsl:when>
            </xsl:choose>
            <xsl:value-of select="concat(' ', .)"/>
        </p>
    </xsl:template>

    <xsl:template match="zoneNotes">
        <xsl:apply-templates/>
    </xsl:template>
    <xsl:template match="noteContenu|noteGenerale|noteResume">
        <p>
            <xsl:choose>
                <xsl:when test="local-name() = 'noteContenu'">
                    <h3>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Note content')"/>
                    </h3>
                </xsl:when>
                <xsl:when test="local-name() = 'noteGenerale'">
                    <h3>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Note generale')"/>
                    </h3>
                </xsl:when>
                <xsl:when test="local-name() = 'noteResume'">
                    <h3>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Note resume')"/>
                    </h3>
                </xsl:when>
            </xsl:choose>
            <xsl:value-of select="concat(' ', .)"/>
        </p>
    </xsl:template>
    <xsl:template match="codeFonction">
        <xsl:param name="code">
            <xsl:value-of select="text()"/>
        </xsl:param>
        <strong>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Function:')"/>
        </strong>
        <xsl:text> </xsl:text>
        <xsl:value-of select="php:function('Bach\IndexationBundle\Entity\PMBAuthor::convertCodeFunction', $code)"/>
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="idNotice">
        <p>
            <strong>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Identifiant notice:')"/>
            </strong>
            <xsl:value-of select="."/>
        </p>
    </xsl:template>

    <xsl:template match="zoneCodageUnimarc">
        <p>
            <strong>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Codage Unimarc:')"/>
            </strong>
            <xsl:value-of select="."/>
        </p>
    </xsl:template>

    <xsl:template match="zoneEditeur">
            <h3>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Editor')"/>
            </h3>
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="zoneCollection225">
        <h3>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Collection 225')"/>
        </h3>
    <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="zoneCollection410">
        <h3>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Collection 410')"/>
        </h3>
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="zoneLangues">
            <h3>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Language:')"/>
            </h3>
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="zoneEditeur/nom|zoneCollection225/nom|zoneCollection225/sousCollection|zoneCollection225/ISSN|zoneCollection410/nom|zoneCollection410/sousCollection|zoneCollection410/ISSN|ville|annee|prenom|dates|zoneIndexationDecimale/s_l|zoneMere/titre|s_t">
        <p>
            <xsl:choose>
                <xsl:when test="local-name() = 's_l'">
                    <h3>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Decimal indexing')"/>
                    </h3>
                </xsl:when>
                <xsl:when test="local-name() = 'titre'">
                    <h3>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Zone Mother')"/>
                    </h3>
                </xsl:when>
                <xsl:when test="local-name() = 'prenom'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Firstname:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'nom' and local-name(parent::node())='zoneEditeur'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Name:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'nom' and local-name(parent::node())='zoneCollection410'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Collection:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'nom' and local-name(parent::node())='zoneCollection225'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Collection:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'nom'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Lastname:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'annee'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Year:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'f_411'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'f_411:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'dates'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Dates:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'ville'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'City:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'sousCollection'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Sub Collection:')"/>
                    </strong>
                </xsl:when>
                <xsl:when test="local-name() = 'ISSN'">
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'ISSN:')"/>
                    </strong>
                </xsl:when>
            </xsl:choose>
            <xsl:value-of select="concat(' ', .)"/>
        </p>
    </xsl:template>

    <xsl:template match="langueDocument|langueOriginale">
        <xsl:param name="clangue">
                <xsl:value-of select="text()"/>
        </xsl:param>
            <p>
            
                <xsl:choose>
                    <xsl:when test="local-name() = 'langueDocument'">
                        <strong>
                            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Language document:')"/>
                        </strong>
                    </xsl:when>
                    <xsl:when test="local-name() = 'langueOriginale'">
                        <strong>
                            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Language original:')"/>
                        </strong>
                    </xsl:when>

                </xsl:choose>
                <xsl:value-of select="concat(' ',php:function('Bach\IndexationBundle\Entity\PMBLanguage::convertCodeLanguage', $clangue))"/>
            </p>
        </xsl:template>


    <xsl:template match="zoneAuteurPrincipal|zoneAuteurPrincipalCollectivite|zoneAuteursSecondaires">
        <xsl:choose>
            <xsl:when test="local-name() = 'zoneAuteurPrincipal'">
                <h3>
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Primary Author')"/>
                </h3>
            </xsl:when>
            <xsl:when test="local-name() = 'zoneAuteurPrincipalCollectivite'">
                <h3>
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Primary Author collectivity')"/>
                </h3>
            </xsl:when>
            <xsl:when test="local-name() = 'zoneAuteursSecondaires'">
                <h3>
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Secondary Author')"/>
                </h3>
            </xsl:when>
            <xsl:when test="local-name() = 'zoneAuteursSecondairesCollectivite'">
                <h3>
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Secondary Author collectivity')"/>
                </h3>
            </xsl:when>
            <xsl:when test="local-name() = 'zoneAuteursAutres'">
                <h3>
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Other Author')"/>
                </h3>
            </xsl:when>
        </xsl:choose>
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="siteWeb">
        <p>
            <strong>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'WebSite:')"/>
            </strong>
            <xsl:text> </xsl:text>
            <xsl:element name="a">
              <xsl:attribute name="href"><xsl:value-of select="."/></xsl:attribute>
              <xsl:attribute name="target">_blank</xsl:attribute>
              <xsl:value-of select="."/>
            </xsl:element>
        </p>
    </xsl:template>

    <xsl:template match="zoneMentionEdition">
        <p>
            <strong>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Mention editor:')"/>
            </strong>
            <xsl:value-of select="."/>
        </p>
    </xsl:template>
    <xsl:template match="f_411">
        <h3>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'f_411:')"/>
        </h3>
        <p>

            <xsl:value-of select="s_t"/>
        </p>
    </xsl:template>

    <xsl:template match="zoneMotsClesLibres">
        <h3>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Keywords:')"/>
        </h3>
        <p>
            <xsl:call-template name="split">
                <xsl:with-param name="text">
                    <xsl:value-of select="."/>
                </xsl:with-param>
            </xsl:call-template>
        </p>
    </xsl:template>

    <xsl:template name="split">
        <xsl:param name="text"/>
        <xsl:param name="first" select="'true'"/>
        <xsl:param name="sep" select="';'"/>
        <xsl:choose>
            <xsl:when test="contains($text,$sep)">
                <xsl:if test="$first = 'false'">
                    <xsl:text>, </xsl:text>
                </xsl:if>
                <xsl:value-of select="substring-before($text, $sep)"/>
                <xsl:call-template name="split">
                    <xsl:with-param name="text">
                        <xsl:value-of select="substring-after($text, $sep)"/>
                    </xsl:with-param>
                    <xsl:with-param name="first">false</xsl:with-param>
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$text"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="zoneCategories[1]">
        <h3>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Category:')"/>
        </h3>
        <p>
            <xsl:for-each select="//zoneCategories">
                <xsl:value-of select="categorie"/>
                <xsl:if test="following-sibling::*[local-name() = 'zoneCategories']">
                    <xsl:text>, </xsl:text>
                </xsl:if>
            </xsl:for-each>
        </p>
    </xsl:template>

    <xsl:template match="zoneIndexationDecimale">
        <p>
            <xsl:apply-templates select="s_l"/>
        </p>
    </xsl:template>

    <xsl:template match="zoneLangues">
        <p>
            <xsl:apply-templates/>
        </p>
    </xsl:template>
    <xsl:template match="zoneMere">
        <p>
            <xsl:apply-templates select="titre"/>
        </p>
    </xsl:template>

    <!-- Per default, display nothing -->
    <xsl:template match="*|@*|node()"/>
    <xsl:template match="*|@*|node()" mode="full"/>
    <xsl:template match="*|@*|node()" mode="resume"/>

</xsl:stylesheet>
