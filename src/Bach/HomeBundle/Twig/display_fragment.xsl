<?xml version="1.0" encoding="UTF-8"?>
<!--

Displays an EAD fragment as HTML

@author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
@license  Unknown http://unknown.com
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
    <xsl:param name="viewer_uri" select="''"/>
    <xsl:param name="cdc" select="'false'"/>
    <xsl:param name="docid"/>

    <xsl:template match="c|c01|c02|c03|c04|c05|c06|c07|c08|c09|c10|c11|c12|archdesc">
        <xsl:variable name="id">
            <xsl:choose>
                <xsl:when test="@id">
                    <xsl:value-of select="@id"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="generate-id(.)"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <div class="content" id="{$id}">
            <xsl:choose>
                <xsl:when test="$full = 1">
                    <xsl:if test="$cdc = 'false'">
                        <ul class="access">
                            <li><a href="#{$id}"><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Content')"/></a></li>
                            <xsl:if test=".//dao|.//daoloc">
                                <li><a href="#relative_documents"><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Documents')"/></a></li>
                            </xsl:if>
                            <xsl:if test="not($children = '')">
                                <li><a href="#children_documents"><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Sub-units')"/></a></li>
                            </xsl:if>
                            <li><a href="__path_add_comment__"><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Add comment')"/></a></li>
                        </ul>
                    </xsl:if>

                    <xsl:apply-templates mode="full"/>

                    <xsl:if test=".//dao|.//daoloc">
                        <figure id="relative_documents">
                            <header>
                                <h3><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Relative documents')"/></h3>
                            </header>
                            <!--<xsl:apply-templates select=".//dao|.//daoloc" mode="daos"/>-->
                            <xsl:variable name="daogrps" select=".//daogrp"/>
                            <xsl:variable name="daos" select=".//dao[not(parent::daogrp)]|.//daoloc[not(parent::daogrp)]"/>
                            <xsl:copy-of select="php:function('Bach\HomeBundle\Twig\DisplayDao::displayDaos', $daogrps, $daos, $viewer_uri, 'medium', $ajax)"/>
                        </figure>
                    </xsl:if>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:apply-templates mode="resume"/>
                </xsl:otherwise>
            </xsl:choose>
        </div>
    </xsl:template>

    <xsl:template match="did" mode="full">
        <section class="did">
            <xsl:if test="not(unittitle)">
                <header>
                    <h2 property="dc:title"><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Untitled unit')"/></h2>
                </header>
            </xsl:if>
            <xsl:apply-templates mode="full"/>
        </section>
    </xsl:template>

    <xsl:template match="unittitle" mode="full">
        <header>
            <h2 property="dc:title"><xsl:apply-templates mode="full"/></h2>
            <xsl:if test="../unitid">
                <span class="unitid" property="dc:identifier">
                    <xsl:if test="../unitid/@label">
                        <xsl:value-of select="concat(../unitid/@label, ' ')"/>
                    </xsl:if>
                    <xsl:value-of select="../unitid"/>
                </span>
            </xsl:if>
            <xsl:if test="../unitdate">
                <xsl:if test="../unitid"> - </xsl:if>
                <span class="date" property="dc:date">
                    <xsl:if test="../unitdate/@normal">
                        <xsl:attribute name="content">
                            <xsl:value-of select="../unitdate/@normal"/>
                        </xsl:attribute>
                    </xsl:if>
                    <xsl:if test="../unitdate/@label">
                        <xsl:value-of select="concat(../uinitdate/@label, ' ')"/>
                    </xsl:if>
                    <xsl:value-of select="../unitdate"/>
                </span>
            </xsl:if>
        </header>
    </xsl:template>

    <xsl:template match="unitdate" mode="full">
        <xsl:if test="not(parent::unittitle) and not(parent::did)">
            <span class="date" property="dc:date">
                <xsl:if test="@normal">
                    <xsl:attribute name="content">
                        <xsl:value-of select="@normal"/>
                    </xsl:attribute>
                </xsl:if>
                <xsl:if test="@label">
                    <xsl:value-of select="concat(@label, ' ')"/>
                </xsl:if>
                <xsl:value-of select="."/>
            </span>
        </xsl:if>
    </xsl:template>

    <xsl:template match="imprint" mode="full">
        <div class="imprint">
            <header>
                <h3><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Publication informations')"/></h3>
                <xsl:apply-templates mode="full"/>
            </header>
        </div>
    </xsl:template>

    <xsl:template match="subject|geogname|persname|corpname|name|function" mode="full">
        <xsl:if test="not(parent::controlaccess)">
            <a>
                <xsl:attribute name="link">
                    <!-- URL cannot ben generated from here. Let's build a specific value to be replaced -->
                    <xsl:value-of select="concat('%%%', local-name(), '::', string(.), '%%%')"/>
                </xsl:attribute>
                <xsl:value-of select="."/>
            </a>
            <xsl:if test="following-sibling::subject or following-sibling::geogname or following-sibling::persname or following-sibling::corpname or following-sibling::name or following-sibling::function">
                <xsl:text>, </xsl:text>
            </xsl:if>
        </xsl:if>
    </xsl:template>

    <xsl:template match="physdesc" mode="full">
        <section class="physdesc">
            <header>
                <h3>
                    <xsl:choose>
                        <xsl:when test="@label">
                            <xsl:value-of select="@label"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Physical description')"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </h3>
            </header>
            <xsl:apply-templates mode="full"/>
        </section>
    </xsl:template>

    <xsl:template match="genreform|extent|physfacet|dimensions" mode="full">
        <div>
            <strong>
                <xsl:choose>
                    <xsl:when test="@label">
                        <xsl:value-of select="@label"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:choose>
                            <xsl:when test="local-name() = 'genreform'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Gender:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'extent'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Extent:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'physfacet'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Appearance:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'dimensions'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Dimensions:')"/>
                            </xsl:when>
                            <xsl:otherwise>
                                UNKNONWN ELEMENT
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </strong>
            <xsl:text> </xsl:text>
            <xsl:value-of select="."/>
        </div>
    </xsl:template>

    <xsl:key name="indexing" match="subject|geogname|persname|corpname|name|function" use="concat(generate-id(..), '_', local-name())"/>
    <xsl:template match="controlaccess" mode="full">
        <div class="contents">
            <xsl:if test="not(head)">
                <header>
                    <h3><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Descriptors')"/></h3>
                </header>
            </xsl:if>

            <xsl:apply-templates mode="full"/>
            <xsl:for-each select="*[generate-id() = generate-id(key('indexing', concat(generate-id(..), '_', local-name()))[1])]">
                <xsl:sort select="local-name()" data-type="text"/>
                <xsl:variable name="elt" select="local-name()"/>
                <div>
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', concat($elt, ':'))"/>
                        <xsl:text> </xsl:text>
                    </strong>
                    <xsl:for-each select="../*[local-name() = $elt]">
                        <!-- URL cannot ben generated from here. Let's build a specific value to be replaced -->
                        <a link="{concat('%%%', $elt, '::', string(.), '%%%')}" about="{$docid}">
                            <xsl:if test="not(local-name() = 'function')">
                                <xsl:attribute name="property">
                                    <xsl:choose>
                                        <xsl:when test="local-name() = 'subject'">dc:subject</xsl:when>
                                        <xsl:when test="local-name() = 'geogname'">gn:name</xsl:when>
                                        <xsl:otherwise>foaf:name</xsl:otherwise>
                                    </xsl:choose>
                                </xsl:attribute>
                                <xsl:attribute name="content">
                                    <xsl:value-of select="."/>
                                </xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="."/>
                        </a>
                        <xsl:if test="following-sibling::*[local-name() = $elt]">
                            <xsl:text>, </xsl:text>
                        </xsl:if>
                    </xsl:for-each>
                </div>
            </xsl:for-each>

        </div>
    </xsl:template>

    <xsl:template match="title" mode="full">
        <xsl:choose>
            <xsl:when test="not(parent::bibref)">
                <div>
                    <strong>
                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Title:')"/>
                        <xsl:text> </xsl:text>
                    </strong>
                    <xsl:value-of select="."/>
                </div>
            </xsl:when>
            <xsl:otherwise>
                <strong>
                    <xsl:choose>
                        <xsl:when test="parent::bibref/@href">
                            <xsl:variable name="href">
                                <xsl:choose>
                                    <xsl:when test="starts-with(../@href, 'http://')">
                                        <xsl:value-of select="../@href"/>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:value-of select="concat('http://', ../@href)"/>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:variable>
                            <a href="{$href}">
                                <xsl:if test="../@title">
                                    <xsl:attribute name="title">
                                        <xsl:value-of select="../@title"/>
                                    </xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="."/>
                            </a>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="."/>
                        </xsl:otherwise>
                    </xsl:choose>
                </strong>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="scopecontent|odd|custodhist|arrangement|relatedmaterial|bibliography|bioghist|acqinfo|separatedmaterial|otherfindaid|repository" mode="full">
        <section class="{local-name()}">
            <xsl:if test="not(head)">
                <xsl:variable name="count" select="count(ancestor::*/head)"/>
                <header>
                    <xsl:element name="h{$count + 3}">
                        <xsl:choose>
                            <xsl:when test="local-name() = 'scopecontent'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Description:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'custodhist'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Conservation history:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'arrangement'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Arrangement:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'relatedmaterial'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Related material:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'bibliography'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Bibliography:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'bioghist'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Biography or history:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'acqinfo'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Acquisition information:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'separatedmaterial'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Separated material:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'otherfindaid'">
                                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Other finding aid:')"/>
                            </xsl:when>
                            <xsl:when test="local-name() = 'repository'">
                                <xsl:choose>
                                    <xsl:when test="@label">
                                        <xsl:value-of select="@label"/>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Repository:')"/>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:when>
                        </xsl:choose>
                    </xsl:element>
                </header>
            </xsl:if>
            <xsl:apply-templates mode="full"/>
        </section>
    </xsl:template>

    <xsl:template match="table|thead|tbody" mode="full">
        <xsl:element name="{local-name()}">
            <xsl:apply-templates mode="full"/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="tgroup" mode="full">
        <xsl:apply-templates mode="full"/>
    </xsl:template>

    <xsl:template match="row" mode="full">
        <xsl:variable name="parent-name" select="local-name(parent::node())"/>
        <tr>
            <xsl:apply-templates mode="full">
                <xsl:with-param name="parent-name" select="$parent-name"/>
            </xsl:apply-templates>
        </tr>
    </xsl:template>

    <xsl:template match="entry" mode="full">
        <xsl:param name="parent-name"/>
        <xsl:choose>
            <xsl:when test="$parent-name = 'thead'">
                <th>
                    <xsl:apply-templates mode="full"/>
                </th>
            </xsl:when>
            <xsl:otherwise>
                <td>
                    <xsl:apply-templates mode="full"/>
                </td>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="emph" mode="full">
        <xsl:choose>
            <xsl:when test="@render='bold'">
                <strong>
                    <xsl:apply-templates mode="full"/>
                </strong>
            </xsl:when>
            <xsl:when test="@render='italic'">
                <em>
                    <xsl:apply-templates mode="full"/>
                </em>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates mode="full"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="list" mode="full">
        <ul>
            <xsl:apply-templates mode="full"/>
        </ul>
    </xsl:template>

    <xsl:template match="defitem|change" mode="full">
        <dl>
            <xsl:apply-templates mode="full"/>
        </dl>
    </xsl:template>

    <xsl:template match="label" mode="full">
        <xsl:variable name="parent-name" select="local-name(parent::node())"/>
        <xsl:choose>
            <xsl:when test="$parent-name = 'defitem'">
                <dt>
                    <xsl:apply-templates mode="full"/>
                </dt>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates mode="full"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="date" mode="full">
        <xsl:variable name="parent-name" select="local-name(parent::node())"/>
        <xsl:choose>
            <xsl:when test="$parent-name = 'change'">
                <dt>
                    <xsl:apply-templates mode="full"/>
                </dt>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates mode="full"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="item" mode="full">
        <xsl:variable name="parent-name" select="local-name(parent::node())"/>
        <xsl:choose>
            <xsl:when test="$parent-name = 'list'">
                <li>
                    <xsl:apply-templates mode="full"/>
                </li>
            </xsl:when>
            <xsl:when test="$parent-name = 'defitem' or $parent-name = 'change'">
                <dd>
                    <xsl:apply-templates mode="full"/>
                </dd>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates mode="full"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="head" mode="full">
        <!-- Count direct parent that have a head child. That will include current node -->
        <xsl:variable name="count" select="count(ancestor::*/head)"/>
        <header>
            <xsl:element name="h{$count + 2}">
                <xsl:value-of select="."/>
            </xsl:element>
        </header>
    </xsl:template>

    <xsl:template match="bibref" mode="full">
        <xsl:variable name="elt">
            <xsl:choose>
                <xsl:when test="not(parent::p)">
                    <xsl:text>div</xsl:text>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:text>span</xsl:text>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:element name="{$elt}">
            <xsl:attribute name="property">dc:bibliographicCitation</xsl:attribute>
            <xsl:apply-templates mode="full"/>
        </xsl:element>
    </xsl:template>

    <xsl:template match="extref|archref" mode="full">
        <xsl:choose>
            <xsl:when test="@href">
                <xsl:choose>
                    <xsl:when test="not(substring(@href, 1, 8) = 'http://')">
                        <xsl:copy-of select="php:function('Bach\HomeBundle\Twig\DisplayDao::getDao', string(@href), string(@title), $viewer_uri)"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <a href="{@href}">
                            <xsl:if test="@title and . != ''">
                                <xsl:attribute name="title">
                                    <xsl:value-of select="@title"/>
                                </xsl:attribute>
                            </xsl:if>
                            <xsl:if test="@title and . = ''">
                                <xsl:value-of select="@title"/>
                            </xsl:if>
                            <xsl:apply-templates mode="full"/>
                        </a>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates mode="full"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="blockquote" mode="full">
        <blockquote>
            <xsl:apply-templates mode="full"/>
        </blockquote>
    </xsl:template>

    <xsl:template match="p" mode="full">
        <p>
            <xsl:if test="@altrender">
                <xsl:attribute name="class">
                    <xsl:value-of select="@altrender"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:apply-templates mode="full"/>
        </p>
    </xsl:template>

    <xsl:template match="text()" mode="full">
        <xsl:copy-of select="."/>
    </xsl:template>

    <xsl:template match="dao|daoloc" mode="daos">
        <xsl:variable name="title" value="@title"/>
        <xsl:copy-of select="php:function('Bach\HomeBundle\Twig\DisplayDao::getDao', string(@href), $title, $viewer_uri, 'medium')"/>
        <!--<a href="{concat($viewer_uri, '/viewer/', @href)}">
            <img>
                <xsl:attribute name="src">
                    <xsl:value-of select="concat($viewer_uri, '/ajax/img/', @href, '/format/medium')"/>
                </xsl:attribute>
                <xsl:attribute name="alt">
                    <xsl:choose>
                        <xsl:when test="@title">
                            <xsl:value-of select="@title"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="@href"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:attribute>
            </img>
        </a>-->
    </xsl:template>

    <xsl:template match="did" mode="resume">
        <xsl:if test="unitid or unitdate">
            <!-- Title is already displayed, show other items -->
            <header class="did">
                <xsl:if test="unitid">
                    <span class="unitid" property="dc:identifier">
                        <xsl:if test="unitid/@label">
                            <xsl:value-of select="concat(unitid/@label, ' ')"/>
                        </xsl:if>
                        <xsl:value-of select="unitid"/>
                    </span>
                    <xsl:if test="unitdate">
                        <xsl:text> - </xsl:text>
                    </xsl:if>
                </xsl:if>
                <xsl:if test="unitdate">
                    <span class="date" property="dc:date">
                        <xsl:if test="unitdate/@normal">
                            <xsl:attribute name="content">
                                <xsl:value-of select="unitdate/@normal"/>
                            </xsl:attribute>
                        </xsl:if>
                        <xsl:if test="unitdate/@label">
                            <xsl:value-of select="unitdate/@label"/>
                            <xsl:text> </xsl:text>
                        </xsl:if>
                        <xsl:value-of select="unitdate"/>
                    </span>
                </xsl:if>
            </header>
        </xsl:if>
    </xsl:template>

    <xsl:template match="physdesc" mode="resume">
        <section class="physdesc">
            <xsl:if test="@label">
                <header><xsl:value-of select="@label"/></header>
                <xsl:text> </xsl:text>
            </xsl:if>
            <xsl:apply-templates mode="resume"/>
        </section>
    </xsl:template>

    <!--<xsl:template match="genreform|extent" mode="resume">
        <xsl:if test="@label">
            <strong><xsl:value-of select="concat(@label, ' ')"/></strong>
        </xsl:if>
        <xsl:value-of select="."/>
    </xsl:template>-->

    <xsl:template match="lb" mode="full">
        <br/>
    </xsl:template>

    <xsl:template match="lb" mode="resume">
        <br/>
    </xsl:template>

    <xsl:template match="controlaccess" mode="resume">
        <aside class="controlaccess">
            <xsl:if test="head">
                <header><xsl:value-of select="head"/></header>
            </xsl:if>
            <div>
                <xsl:apply-templates mode="resume" />
            </div>
        </aside>
    </xsl:template>

    <xsl:template match="subject|geogname|persname|corpname|name|function|genreform" mode="resume">
        <a>
            <xsl:attribute name="link">
                <!-- URL cannot ben generated from here. Let's build a specific value to be replaced -->
                <xsl:value-of select="concat('%%%', local-name(), '::', string(.), '%%%')"/>
            </xsl:attribute>
            <xsl:if test="not(local-name() = 'function')">
                <xsl:attribute name="property">
                    <xsl:choose>
                        <xsl:when test="local-name() = 'subject'">dc:subject</xsl:when>
                        <xsl:when test="local-name() = 'geogname'">gn:name</xsl:when>
                        <xsl:otherwise>foaf:name</xsl:otherwise>
                    </xsl:choose>
                </xsl:attribute>
                <xsl:attribute name="content">
                    <xsl:value-of select="."/>
                </xsl:attribute>
            </xsl:if>
            <xsl:attribute name="about">
                <xsl:value-of select="$docid"/>
            </xsl:attribute>
            <xsl:value-of select="."/>
        </a>
        <xsl:if test="following-sibling::subject or following-sibling::geogname or following-sibling::persname or following-sibling::corpname or following-sibling::name or following-sibling::function or following-sibling::genreform">
            <xsl:text>, </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- Per default, display nothing -->
    <xsl:template match="*|@*|node()"/>
    <xsl:template match="*|@*|node()" mode="full"/>
    <xsl:template match="*|@*|node()" mode="resume"/>

</xsl:stylesheet>
