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

    <xsl:param name="full" select="1"/>
    <xsl:param name="ajax" select="''"/>
    <xsl:param name="children" select="''"/>
    <xsl:param name="comments_enabled" select="''"/>
    <xsl:param name="comments" select="''"/>
    <xsl:param name="viewer_uri" select="''"/>
    <xsl:param name="covers_dir" select="''"/>
    <xsl:param name="count_subs" select="''"/>
    <xsl:param name="cdc" select="'false'"/>
    <xsl:param name="docid"/>
    
    <xsl:template match="notices/">
            <xsl:value-of select="notice/idNotice"/>
            <xsl:value-of select="notice/zoneCodageUnimarc"/>
                <xsl:value-of select="notice/zoneCodageUnimarc"/>
            <xsl:value-of select="notice/zoneTitre"/>
                <xsl:value-of select="notice/zoneTitre/titrePrincipal"/>
                <xsl:value-of select="notice/zoneTitre/titreComplement"/>
            <xsl:value-of select="notice/prixISBN"/>
                <xsl:value-of select="notice/prixISBN/ISBN"/>
            <xsl:value-of select="notice/zoneLangues"/>
                <xsl:value-of select="notice/zoneLangues/langueDocument"/>
                <xsl:value-of select="notice/zoneLangues/langueOriginale"/>
            <xsl:value-of select="notice/zoneCollation"/>
                <xsl:value-of select="notice/zoneCollation/nbPages"/>
                <xsl:value-of select="notice/zoneCollation/illustration"/>
                <xsl:value-of select="notice/zoneCollation/taille"/>
                <xsl:value-of select="notice/zoneCollation/materielAccompagnement"/>
            <xsl:value-of select="notice/zoneNotes"/>
                <xsl:value-of select="notice/zoneNotes/noteContenu"/>
                <xsl:value-of select="notice/zoneNotes/noteResume"/>
            <xsl:value-of select="notice/zoneAuteurPrincipal"/>
                <xsl:value-of select="notice/zoneAuteurPrincipal/nom"/>
                <xsl:value-of select="notice/zoneAuteurPrincipal/prenom"/>
                <xsl:value-of select="notice/zoneAuteurPrincipal/codeFonction"/>
                <xsl:value-of select="notice/zoneAuteurPrincipal/dates"/>
                <xsl:value-of select="notice/zoneAuteurPrincipal/siteWeb"/>
                <xsl:value-of select="notice/zoneAuteurPrincipal/s_9"/>
            <xsl:value-of select="notice/zoneAuteurPrincipalCollectivite"/>
                <xsl:value-of select="notice/zoneAuteurPrincipalCollectivite/nom"/>
                <xsl:value-of select="notice/zoneAuteurPrincipalCollectivite/prenom"/>
                <xsl:value-of select="notice/zoneAuteurPrincipalCollectivite/codeFonction"/>
                <xsl:value-of select="notice/zoneAuteurPrincipalCollectivite/dates"/>
                <xsl:value-of select="notice/zoneAuteurPrincipalCollectivite/siteWeb"/>
                <xsl:value-of select="notice/zoneAuteurPrincipalCollectivite/s_9"/>
            <xsl:value-of select="notice/zoneAuteursSecondaires"/>
                <xsl:value-of select="notice/zoneAuteursSecondaires/nom"/>
                <xsl:value-of select="notice/zoneAuteursSecondaires/prenom"/>
                <xsl:value-of select="notice/zoneAuteursSecondaires/codeFonction"/>
                <xsl:value-of select="notice/zoneAuteursSecondaires/dates"/>
                <xsl:value-of select="notice/zoneAuteursSecondaires/siteWeb"/>
                <xsl:value-of select="notice/zoneAuteursSecondaires/s_9"/>
            <xsl:value-of select="notice/zoneAuteursSecondairesCollectivite"/>
                <xsl:value-of select="notice/zoneAuteursSecondairesCollectivite/nom"/>
                <xsl:value-of select="notice/zoneAuteursSecondairesCollectivite/prenom"/>
                <xsl:value-of select="notice/zoneAuteursSecondairesCollectivite/codeFonction"/>
                <xsl:value-of select="notice/zoneAuteursSecondairesCollectivite/dates"/>
                <xsl:value-of select="notice/zoneAuteursSecondairesCollectivite/siteWeb"/>
                <xsl:value-of select="notice/zoneAuteursSecondairesCollectivite/s_9"/>
            <xsl:value-of select="notice/zoneAuteursAutres"/>
                <xsl:value-of select="notice/zoneAuteursAutres/nom"/>
                <xsl:value-of select="notice/zoneAuteursAutres/prenom"/>
                <xsl:value-of select="notice/zoneAuteursAutres/codeFonction"/>
                <xsl:value-of select="notice/zoneAuteursAutres/dates"/>
                <xsl:value-of select="notice/zoneAuteursAutres/siteWeb"/>
                <xsl:value-of select="notice/zoneAuteursAutres/s_9"/>
            <xsl:value-of select="notice/zoneEditeur"/>
                <xsl:value-of select="notice/zoneEditeur/ville"/>
                <xsl:value-of select="notice/zoneEditeur/s_b"/>
                <xsl:value-of select="notice/zoneEditeur/nom"/>
                <xsl:value-of select="notice/zoneEditeur/annee"/>
                <xsl:value-of select="notice/zoneEditeur/s_9"/>
            <xsl:value-of select="notice/zoneCollection225"/>
                <xsl:value-of select="notice/zoneCollection225/nom"/>
                <xsl:value-of select="notice/zoneCollection225/s_9"/>
            <xsl:value-of select="notice/zoneCollection410"/>
                <xsl:value-of select="notice/zoneCollection410/nom"/>
                <xsl:value-of select="notice/zoneCollection410/s_9"/>
            <xsl:value-of select="notice/zoneIndexationDecimale"/>
                <xsl:value-of select="notice/zoneIndexationDecimale/nom"/>
                <xsl:value-of select="notice/zoneIndexationDecimale/s_l"/>
                <xsl:value-of select="notice/zoneIndexationDecimale/s_9"/>
            <xsl:value-of select="notice/f_896"/>
                <xsl:value-of select="notice/f_896/s_a"/>
            <xsl:value-of select="notice/zoneMotsClesLibres"/>
                <xsl:value-of select="notice/zoneMotsClesLibres/mot"/>
            <xsl:value-of select="notice/zoneCategories"/>
                <xsl:value-of select="notice/zoneCategories/s_9"/>
                <xsl:value-of select="notice/zoneCategories/categorie"/>
    </xsl:template>


    <!-- Per default, display nothing -->
    <xsl:template match="*|@*|node()"/>
    <xsl:template match="*|@*|node()" mode="full"/>
    <xsl:template match="*|@*|node()" mode="resume"/>

</xsl:stylesheet>