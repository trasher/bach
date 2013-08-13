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
    xmlns:php="http://php.net/xsl">

    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:param name="full" select="1"/>
    <xsl:param name="viewer_uri" select="''"/>

    <xsl:template match="c|c01|c02|c03|c04|c05|c06|c07|c08|c09|c10|c11|c12">
        <div class="content">
            <xsl:if test="@id">
                <xsl:attribute name="id">
                    <xsl:value-of select="@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:choose>
                <xsl:when test="$full = 1">
                    <xsl:apply-templates mode="full"/>
                    <xsl:if test=".//dao|.//daoloc">
                        <figure>
                            <header>
                                <h3><xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Relative documents')"/></h3>
                            </header>
                            <!--<xsl:apply-templates select=".//dao|.//daoloc" mode="daos"/>-->
                            <xsl:variable name="daogrps" select=".//daogrp"/>
                            <xsl:variable name="daos" select=".//dao[not(parent::daogrp)]|.//daoloc[not(parent::daogrp)]"/>
                            <xsl:copy-of select="php:function('Bach\HomeBundle\Twig\DisplayDao::displayDaos', $daogrps, $daos, $viewer_uri, 'medium')"/>
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
        <header class="did">
            <xsl:apply-templates mode="full"/>
            <!--<h2><xsl:value-of select="unittitle"/></h2>
            <span class="date">
                <xsl:if test="unitdate/@label">
                    <xsl:value-of select="unitdate/@label"/>
                </xsl:if>
                <xsl:value-of select="unitdate"/>
            </span>-->
        </header>
    </xsl:template>

    <xsl:template match="unittitle" mode="full">
        <h2><xsl:value-of select="."/></h2>
        <xsl:apply-templates mode="full"/>
    </xsl:template>

    <xsl:template match="unitdate" mode="full">
        <xsl:if test="not(parent::unittitle)">
            <span class="date">
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
        <header class="physdesc">
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
            <xsl:apply-templates mode="full"/>
        </header>
    </xsl:template>

    <xsl:template match="genreform" mode="full">
        <div>
            <xsl:choose>
                <xsl:when test="@label">
                    <xsl:value-of select="@label"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Gender:')"/>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:text> </xsl:text>
            <xsl:value-of select="."/>
        </div>
    </xsl:template>

    <xsl:template match="extent" mode="full">
        <div>
            <xsl:choose>
                <xsl:when test="@label">
                    <xsl:value-of select="@label"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Extent:')"/>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:text> </xsl:text>
            <xsl:value-of select="."/>
        </div>
    </xsl:template>

    <xsl:key name="indexing" match="subject|geogname|persname|corpname|name|function" use="concat(generate-id(..), '_', local-name())"/>
    <xsl:template match="controlaccess" mode="full">
        <div class="contents">
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
                        <a link="{concat('%%%', $elt, '::', string(.), '%%%')}">
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
        <div>
            <strong>
                <xsl:value-of select="php:function('Bach\HomeBundle\Twig\DisplayEADFragment::i18nFromXsl', 'Title:')"/>
                <xsl:text> </xsl:text>
            </strong>
            <xsl:value-of select="."/>
        </div>
    </xsl:template>

    <xsl:template match="scopecontent|odd" mode="full">
        <xsl:apply-templates mode="full"/>
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

    <xsl:template match="head" mode="full">
        <!-- Count direct parent that have a head child. That will include current node -->
        <xsl:variable name="count" select="count(ancestor::*/head)"/>
        <header>
            <xsl:element name="h{$count + 2}">
                <xsl:value-of select="."/>
            </xsl:element>
        </header>
    </xsl:template>

    <xsl:template match="p" mode="full">
        <p>
            <xsl:apply-templates mode="full"/>
        </p>
    </xsl:template>

    <xsl:template match="text()" mode="full">
        <xsl:copy-of select="."/>
    </xsl:template>

    <xsl:template match="dao|daoloc" mode="daos">
        <xsl:copy-of select="php:function('Bach\HomeBundle\Twig\DisplayDao::getDao', string(@href), $viewer_uri, 'medium')"/>
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
        <!-- Title is already displayed, show other items -->
        <header class="did">
            <span class="date">
                <xsl:if test="unitdate/@label">
                    <xsl:value-of select="unitdate/@label"/>
                    <xsl:text> </xsl:text>
                </xsl:if>
                <xsl:value-of select="unitdate"/>
            </span>
        </header>
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

    <xsl:template match="genreform|extent" mode="resume">
        <xsl:if test="@label">
            <strong><xsl:value-of select="concat(@label, ' ')"/></strong>
        </xsl:if>
        <xsl:value-of select="."/>
    </xsl:template>

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

    <xsl:template match="subject|geogname|persname|corpname|name|function" mode="resume">
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
    </xsl:template>

    <!-- Per default, display nothing -->
    <xsl:template match="*|@*|node()"/>
    <xsl:template match="*|@*|node()" mode="full"/>
    <xsl:template match="*|@*|node()" mode="resume"/>

</xsl:stylesheet>
