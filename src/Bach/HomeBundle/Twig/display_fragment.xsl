<?xml version="1.0" encoding="UTF-8"?>
<!--

Displays an EAD fragment as HTML

@author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
@license  Unknown http://unknown.com
@link     http://anaphore.eu
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:param name="full"/>

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
                    <xsl:text> : </xsl:text>
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
                    <xsl:value-of select="@label"/>
                    <xsl:text> : </xsl:text>
                </xsl:if>
                <xsl:value-of select="."/>
            </span>
        </xsl:if>
    </xsl:template>

    <xsl:template match="imprint" mode="full">
        <div class="imprint">
            <header>
                <h3>Informations de publication</h3>
                <xsl:apply-templates mode="full"/>
            </header>
        </div>
    </xsl:template>

    <xsl:template match="geogname" mode="full">
        <span class="geogname">
            <xsl:choose>
                <xsl:when test="@label">
                    <xsl:value-of select="@label"/>
                    <xsl:text> : </xsl:text>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:text>Lieu géographique : </xsl:text>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:value-of select="."/>
        </span>
    </xsl:template>

    <xsl:template match="physdesc" mode="full">
        <header class="physdesc">
            <h3>
                <xsl:choose>
                    <xsl:when test="@label">
                        <xsl:value-of select="@label"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text>Description physique</xsl:text>
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
                    <xsl:text>Genre : </xsl:text>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:value-of select="."/>
        </div>
    </xsl:template>

    <xsl:template match="extent" mode="full">
        <div>
            <xsl:choose>
                <xsl:when test="@label">
                    <xsl:value-of select="@label"/>
                    <xsl:text> : </xsl:text>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:text>Extent : </xsl:text>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:value-of select="."/>
        </div>
    </xsl:template>

    <xsl:template match="controlaccess" mode="full">
        <div class="contents">
            <xsl:if test="head">
                <header>
                    <h3><xsl:value-of select="head"/></h3>
                </header>
            </xsl:if>
            <xsl:apply-templates mode="full"/>
        </div>
    </xsl:template>

    <xsl:template match="corpname" mode="full">
        <div>
            <strong>
                <xsl:choose>
                    <xsl:when test="@label">
                        <xsl:value-of select="@label"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text>Organisme</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:if test="@role">
                    <xsl:value-of select="concat(' ', @role)"/>
                </xsl:if>
                <xsl:text> : </xsl:text>
            </strong>
            <xsl:value-of select="."/>
        </div>
    </xsl:template>

    <xsl:template match="title" mode="full">
        <div>
            <strong>Titre : </strong>
            <xsl:value-of select="."/>
        </div>
    </xsl:template>

    <xsl:template match="did" mode="resume">
        <!-- Title is already displayed, show other items -->
        <header class="did">
            <span class="date">
                <xsl:if test="unitdate/@label">
                    <xsl:value-of select="unitdate/@label"/>
                    <xsl:text> : </xsl:text>
                </xsl:if>
                <xsl:value-of select="unitdate"/>
            </span>
        </header>
    </xsl:template>

    <xsl:template match="physdesc" mode="resume">
        <section class="physdesc">
            <xsl:if test="@label">
                <header><xsl:value-of select="@label"/></header>
            </xsl:if>
            <xsl:apply-templates mode="resume"/>
        </section>
    </xsl:template>

    <xsl:template match="genreform|extent" mode="resume">
        <xsl:if test="@label">
            <strong><xsl:value-of select="@label"/></strong>
        </xsl:if>
        <xsl:value-of select="."/>
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

    <xsl:template match="subject|geogname|persname|corpname|name" mode="resume">
        <a>
            <xsl:attribute name="link">
                <!-- URL cannot ben generated from here. Let's build a specific value to be replaced -->
                <xsl:value-of select="concat('%%%', local-name(), '::', string(.), '%%%')"/>
            </xsl:attribute>
            <xsl:value-of select="."/>
        </a>
        <xsl:if test="following-sibling::subject or following-sibling::geogname or following-sibling::persname or following-sibling::corpname or following-sibling::name">
            <xsl:text>, </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- Per default, display nothing -->
    <xsl:template match="*|@*|node()"/>
    <xsl:template match="*|@*|node()" mode="full"/>
    <xsl:template match="*|@*|node()" mode="resume"/>

</xsl:stylesheet>
