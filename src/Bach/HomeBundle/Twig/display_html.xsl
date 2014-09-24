<?xml version="1.0" encoding="UTF-8"?>
<!--

Displays an EAD document as HTML

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

@author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
@license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
@link     http://anaphore.eu
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl"
    exclude-result-prefixes="php">

    <xsl:output method="html" omit-xml-declaration="yes"/>
    <xsl:param name="docid" select="''"/>
    <xsl:param name="expanded" select="'false'"/>

    <xsl:template match="ead">
        <article>
            <header>
                <xsl:apply-templates select="eadheader" mode="header"/>
            </header>
            <div id="inventory_contents">
                <h3 id="presentation">
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Presentation')"/>
                </h3>
                <div id="inventory_presentation">
                    <table>
                        <xsl:apply-templates mode="presentation"/>
                    </table>
                </div>
                <h3 id="contents">
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Contents')"/>
                </h3>
                <div class="css-treeview">
                    <ul>%contents%</ul>
                </div>
                <h3 id="biblioinfos">
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Bibliographic informations')"/>
                </h3>
                <div>
                    <xsl:apply-templates select="eadheader/*" mode="header"/>
                </div>
            </div>
        </article>
    </xsl:template>

    <xsl:template match="eadheader" mode="header">
        <h2>
            <xsl:apply-templates select="filedesc/titlestmt/titleproper" mode="header_title"/>
        </h2>
        <xsl:apply-templates select="filedesc/titlestmt/author" mode="header_title"/>
    </xsl:template>

    <xsl:template match="titleproper" mode="header_title">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="author" mode="header_title">
        <div class="inventory_author">
            <xsl:apply-templates/>
        </div>
    </xsl:template>

    <xsl:template match="archdesc" mode="presentation">
        <xsl:apply-templates mode="presentation"/>
    </xsl:template>

    <xsl:template match="did" mode="presentation">
        <xsl:apply-templates mode="presentation"/>
    </xsl:template>

    <xsl:template match="unitid|unittitle|unitdate|extent" mode="presentation">
        <tr>
            <th>
                <xsl:choose>
                    <xsl:when test="@label">
                        <xsl:value-of select="@label"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:choose>
                            <xsl:when test="local-name() = 'unittitle'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Title')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'unitdate'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Date')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'unitid'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Class number')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'extent'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Extent')"/>
                            </xsl:when>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </th>
            <td>
                <xsl:value-of select="."/>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="physdesc" mode="presentation">
        <tr>
            <th>
                <xsl:choose>
                    <xsl:when test="@label">
                        <xsl:value-of select="@label"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Physical description')"/>
                    </xsl:otherwise>
                </xsl:choose>
            </th>
            <td>
                <xsl:apply-templates mode="contents"/>
            </td>
        </tr>
        <xsl:apply-templates mode="presentation"/>
    </xsl:template>

    <xsl:template match="custodhist|acqinfo" mode="presentation">
        <tr>
            <th>
                <xsl:choose>
                    <xsl:when test="head">
                        <xsl:value-of select="head"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:choose>
                            <xsl:when test="local-name() ='custodhist'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Custodial history')"/>
                            </xsl:when>
                            <xsl:when test="local-name() ='acqinfo'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Acquisition information')"/>
                            </xsl:when>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </th>
            <td>
                <xsl:apply-templates mode="contents"/>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="accessrestrict" mode="presentation">
        <xsl:choose>
            <xsl:when test="legalstatus">
                <tr>
                    <th>
                        <xsl:choose>
                            <xsl:when test="head">
                                <xsl:value-of select="head"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Legal status')"/>
                            </xsl:otherwise>
                        </xsl:choose>
                    </th>
                    <td><xsl:value-of select="legalstatus"/></td>
                </tr>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates mode="presentation"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="controlaccess" mode="presentation">
        <xsl:variable name="nodes" select="subject|geogname|persname|corpname|name|function|genreform[not(@source='liste-niveau') and not(@source='liste-typedocAC') and not(@type='typir')]"/>
        <xsl:copy-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::showDescriptors', $nodes, $docid)/descriptors/*"/>
    </xsl:template>

    <xsl:template match="genreform[@source = 'liste-typedocAC']" mode="contents"/>
    <xsl:template match="physdesc/extent" mode="contents"/>
    <xsl:template match="custodhist/head|acqinfo/head" mode="contents"/>

    <!--<xsl:template match="archdesc" mode="header">
        <div id="docheader">
            <xsl:apply-templates mode="header"/>
        </div>
    </xsl:template>-->

    <!-- ***** FILEDESC ***** -->
    <xsl:template match="filedesc" mode="header">
        <section class="{local-name()}">
            <header>
                <h3>
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Publication informations')"/>
                </h3>
            </header>
            <xsl:apply-templates mode="header"/>
        </section>
    </xsl:template>

    <xsl:template match="filedesc/titlestmt" mode="header">
        <h4>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Title statement')"/>
        </h4>

        <!-- Title proper -->
        <strong>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Title proper:')"/>
        </strong>
        <xsl:choose>
            <xsl:when test="count(titleproper) > 1">
                <ul>
                    <xsl:apply-templates select="titleproper" mode="header"/>
                </ul>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="' '"/>
                <xsl:apply-templates select="titleproper"/>
            </xsl:otherwise>
        </xsl:choose>

        <!-- Subtitle -->
        <xsl:if test="subtitle">
            <br/>
            <strong>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Subtitle:')"/>
            </strong>
            <xsl:choose>
                <xsl:when test="count(subtitle) > 1">
                    <ul>
                        <xsl:apply-templates select="subtitle" mode="header"/>
                    </ul>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="' '"/>
                    <xsl:apply-templates select="subtitle"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:if>

        <!-- Author -->
        <xsl:if test="author">
            <br/>
            <strong>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Author:')"/>
            </strong>
            <xsl:value-of select="' '"/>
            <xsl:apply-templates select="author"/>
        </xsl:if>

        <!-- Sponsor -->
        <xsl:if test="sponsor and normalize-space(sponsor) != ''">
            <br/>
            <strong>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Sponsor:')"/>
            </strong>
            <xsl:value-of select="' '"/>
            <xsl:apply-templates select="sponsor"/>
        </xsl:if>
    </xsl:template>

    <xsl:template match="filedesc/titlestmt[ count(titleproper) > 1 ]/titleproper" mode="header">
        <li>
            <xsl:apply-templates />
        </li>
    </xsl:template>

    <xsl:template match="filedesc/titlestmt[ count(subtitle) > 1 ]/subtitle" mode="header">
        <li>
            <xsl:apply-templates />
        </li>
    </xsl:template>

    <xsl:template match="filedesc/titlestmt/author" mode="header">
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="filedesc/titlestmt/sponsor" mode="header">
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="filedesc/editionstmt" mode="header">
        <h4>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Edition statement')"/>
        </h4>
        <xsl:apply-templates mode="header"/>
    </xsl:template>

    <xsl:template match="filedesc/publicationstmt" mode="header">
        <h4>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Publication statement')"/>
        </h4>
        <xsl:apply-templates mode="header" />
    </xsl:template>

    <xsl:template match="filedesc/publicationstmt/publisher" mode="header">
        <strong>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Publisher:')"/>
        </strong>
        <xsl:value-of select="' '"/>
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="filedesc/publicationstmt/date" mode="header">
        <br/>
        <strong>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Date:')"/>
        </strong>
        <xsl:value-of select="' '"/>
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="filedesc/publicationstmt/address" mode="header">
        <br/>
        <strong>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Address:')"/>
        </strong>
        <xsl:value-of select="' '"/>
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="filedesc/publicationstmt/num|filedesc/seriesstmt/num" mode="header">
        <br/>
        <strong>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Number:')"/>
        </strong>
        <xsl:value-of select="' '"/>
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="filedesc/publicationstmt/p|filedesc/editionstmt/p|filedesc/editionstmt/edition|filedesc/seriesstmt/p|filedesc/notestmt/note/p" mode="header">
        <p>
            <xsl:apply-templates />
        </p>
    </xsl:template>

    <xsl:template match="filedesc/seriesstmt" mode="header">
        <h4>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Series statement')"/>
        </h4>
        <xsl:apply-templates mode="header" />
    </xsl:template>

    <xsl:template match="filedesc/seriesstmt/titleproper" mode="header">
        <strong>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Title proper:')"/>
        </strong>
        <xsl:value-of select="' '"/>
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="filedesc/notestmt" mode="header">
        <h4>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Note statement')"/>
        </h4>
        <xsl:apply-templates mode="header" />
    </xsl:template>

    <xsl:template match="filedesc/notestmt/note" mode="header">
        <div>
            <xsl:apply-templates/>
        </div>
    </xsl:template>

    <xsl:template match="profiledesc" mode="header">
        <xsl:if test="*">
            <section class="{local-name()}">
                <header>
                    <h3>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Profile')"/>
                    </h3>
                </header>
                <xsl:apply-templates mode="header"/>
            </section>
        </xsl:if>
    </xsl:template>

    <xsl:template match="profiledesc/creation" mode="header">
        <strong>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Creation:')"/>
        </strong>
        <xsl:value-of select="' '"/>
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="profiledesc/langusage" mode="header">
        <br/>
        <strong>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Language:')"/>
        </strong>
        <xsl:value-of select="' '"/>
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="profiledesc/descrules" mode="header">
        <br/>
        <strong>
            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Description rules:')"/>
        </strong>
        <xsl:value-of select="' '"/>
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="revisiondesc" mode="header">
        <section class="{local-name()}">
            <header>
               <h3>
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayHtml::i18nFromXsl', 'Revision description')"/>
                </h3>
            </header>
            <xsl:apply-templates mode="header"/>
        </section>
    </xsl:template>

    <xsl:template match="revisiondesc/change" mode="header">
        <xsl:choose>
            <xsl:when test="count(item) = 1 ">
                <xsl:apply-templates select="item" mode="header"/>
            </xsl:when>
            <xsl:otherwise>
                <ul>
                    <xsl:apply-templates select="item" mode="header"/>
                </ul>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="revisiondesc/change[count(item) > 1]/item" mode="header">
        <li>
            <xsl:apply-templates />
        </li>
    </xsl:template>

    <xsl:template match="revisiondesc/change[count(item) = 1]/item" mode="header">
        <p><xsl:apply-templates /></p>
    </xsl:template>
    <!-- ***** END FILEDESC ***** -->

    <!-- ***** CONTENTS ***** -->
    <xsl:template match="did">
        <xsl:if test="not(unittitle)">
            <h2 property="dc:title"><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Untitled unit')"/></h2>
        </xsl:if>
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="unittitle">
        <a class="display_doc">
            <!-- URL cannot ben generated from here. Let's build a specific value to be replaced -->
            <xsl:attribute name="link">
                <xsl:choose>
                    <xsl:when test="not(ancestor::c[1])">
                        <xsl:value-of select="concat('%%%', $docid, '_description%%%')"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="concat('%%%', $docid, '_', ancestor::c[1]/@id, '%%%')"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>

            <strong property="dc:title">
                <xsl:apply-templates />
            </strong>

            <xsl:if test="../unitdate and not(./unitdate)">
                <span class="date" property="dc:date">
                    <strong><xsl:value-of select="concat(' • ', ../unitdate)"/></strong>
                </span>
            </xsl:if>

            <xsl:if test="../unitid[not(@type='ordre_c')]">
                <xsl:text> - </xsl:text>
                <span class="unitid" property="dc:identifier">
                    <xsl:value-of select="../unitid[not(@type='ordre_c')]"/>
                </span>
            </xsl:if>
        </a>
    </xsl:template>
    <!-- ***** END CONTENTS ***** -->

    <!-- ***** GENERIC TAGS ***** -->
    <xsl:template match="date|language">
        <xsl:value-of select="' '"/>
        <xsl:apply-templates/>
        <xsl:value-of select="' '"/>
    </xsl:template>

    <xsl:template match="titleproper|author|sponsor|addressline|subtitle">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="unittitle/unitdate">
        <span class="date" property="dc:date">
            <xsl:value-of select="' '"/>
            <xsl:value-of select="."/>
        </span>
    </xsl:template>

    <xsl:template match="emph">
        <xsl:choose>
            <xsl:when test="@render='bold'">
                <strong>
                    <xsl:apply-templates/>
                </strong>
            </xsl:when>
            <xsl:when test="@render='italic'">
                <em>
                    <xsl:apply-templates/>
                </em>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="text()">
        <xsl:copy-of select="normalize-space(.)"/>
    </xsl:template>

    <xsl:template match="lb">
        <xsl:if test="not(preceding-sibling::lb)">
            <br/>
        </xsl:if>
    </xsl:template>
    <!-- ***** END GENERIC TAGS ***** -->

    <!-- Per default, display nothing -->
    <xsl:template match="*|@*|node()"/>
    <xsl:template match="*|@*|node()" mode="header"/>
    <xsl:template match="*|@*|node()" mode="presentation"/>

</xsl:stylesheet>
