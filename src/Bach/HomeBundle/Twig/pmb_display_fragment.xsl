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

    <xsl:template match="idNotice">
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Identifiant notice : ')"/><xsl:value-of select="."/></p>
    </xsl:template>
    <xsl:template match="zoneCodageUnimarc">
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Codage Unimarc : ')"/><xsl:value-of select="."/></p>
    </xsl:template>
    <xsl:template match="zoneEditeur">
        <h3><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Editor')"/></h3>
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Nom : ')"/><xsl:value-of select="nom"/></p>
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'City : ')"/><xsl:value-of select="ville"/></p>
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Year : ')"/><xsl:value-of select="annee"/></p>
    </xsl:template>
    <xsl:template match="zoneAuteurPrincipal">
        <h3><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Primary Author')"/></h3>
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Firstname : ')"/><xsl:value-of select="prenom"/></p>
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Lastname : ')"/><xsl:value-of select="nom"/></p>
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Function : ')"/><xsl:value-of select="codeFonction"/></p>
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Dates : ')"/><xsl:value-of select="dates"/></p>
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'WebSite : ')"/><xsl:value-of select="siteWeb"/></p>
    </xsl:template>
    <xsl:template match="zoneMentionEdition">
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Mention editor : ')"/><xsl:value-of select="."/></p>
    </xsl:template>
    <xsl:template match="f_411">
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'f_411 : ')"/><xsl:value-of select="."/></p>
    </xsl:template>
    <xsl:template match="zoneMotsClesLibres">
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Keywords : ')"/><xsl:value-of select="."/></p>
    </xsl:template>
        <xsl:template match="zoneCategories">
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Category : ')"/><xsl:value-of select="categorie"/></p>
    </xsl:template>
    <xsl:template match="zoneCategories">
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Category : ')"/><xsl:value-of select="categorie"/></p>
    </xsl:template>
       <xsl:template match="zoneCollection410">
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', '410 : ')"/><xsl:value-of select="nom"/></p>
    </xsl:template>
       <xsl:template match="zoneCollection225">
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', '225 : ')"/><xsl:value-of select="nom"/></p>
    </xsl:template>

    <!--<xsl:template match="zoneTitre/*"/>
    <xsl:template match="titreComplement|titrePropreAuteurDiffzoneTitre|titreParallele">
        <p>
            <xsl:value-of select="."/>
        </p>
    </xsl:template>-->
    <xsl:template match="/langueDocument">
        <p><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Language : ')"/><xsl:value-of select="."/></p>
    </xsl:template>
    <xsl:template match="zoneNotes">
        <h3><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayPMBFragment::i18nFromXsl', 'Notes')"/></h3>
        <p><xsl:value-of select="."/></p>
    </xsl:template>

    <!-- Per default, display nothing -->
    <xsl:template match="*|@*|node()"/>
    <xsl:template match="*|@*|node()" mode="full"/>
    <xsl:template match="*|@*|node()" mode="resume"/>

</xsl:stylesheet>