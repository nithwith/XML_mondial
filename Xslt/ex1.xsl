<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    <xsl:output method="xml" indent="yes" doctype-system="em.dtd"/>

    <!--Template de création de l'élément racine-->
    <xsl:template match="/">
        <xsl:element name="em">

            <xsl:element name="liste-pays">
                <xsl:apply-templates select="id(/mondial/country[/mondial/river[./to/@watertype eq 'sea']/tokenize(@country, '\s+') = @car_code  or /mondial/sea/tokenize(@country, '\s+') =  @car_code]/@car_code)" mode="pays"/>
            </xsl:element>

            <xsl:element name="liste-espace-maritime">
                <xsl:choose>
                    <xsl:when test="type">
                        <xsl:apply-templates select="/mondial/sea">
                            <xsl:with-param name="t" select="type"/>
                        </xsl:apply-templates>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:apply-templates select="/mondial/sea">
                            <xsl:with-param name="t" select="'inconnu'"/>
                        </xsl:apply-templates>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:element>

        </xsl:element>
    </xsl:template>

    <!-- Template de création des pays   -->
    <xsl:template match="country" mode="pays">
        <xsl:element name="pays">
            <xsl:attribute name="id-p">
                <xsl:value-of select="@car_code"/>
            </xsl:attribute>
            <xsl:attribute name="nom-p">
                <xsl:value-of select="name"/>
            </xsl:attribute>
            <xsl:attribute name="superficie">
                <xsl:value-of select="@area"/>
            </xsl:attribute>
            <xsl:attribute name="nbhab">
                <xsl:value-of select="population[last()]"/>
            </xsl:attribute>
            <xsl:apply-templates
                select="/mondial/river[./to/@watertype eq 'sea' and source/@country eq current()/@car_code]"
            />
        </xsl:element>
    </xsl:template>

    <!-- Template de création des fleuves dans les pays-->
    <xsl:template match="river">
        <xsl:variable name="countries" select="id(./@country)"/>
        <xsl:element name="fleuve">
            <xsl:attribute name="id-f">
                <xsl:value-of select="@id"/>
            </xsl:attribute>
            <xsl:attribute name="nom-f">
                <xsl:value-of select="name"/>
            </xsl:attribute>
            <xsl:attribute name="longueur">
                <xsl:value-of select="length"/>
            </xsl:attribute>
            <xsl:attribute name="se-jette">
                <xsl:value-of select="to/@water"/>
            </xsl:attribute>
            <xsl:choose>
<!--                Si il n'y a qu'un pays dans le parcourt du fleuve on affiche la distance du fleuve dans ce pays sinon on affiche inconnu-->
                <xsl:when test="count($countries) &lt;= 1">
                    <xsl:apply-templates select="$countries" mode="parcourt">
                        <xsl:with-param name="dist" select="length"/>
                    </xsl:apply-templates>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:apply-templates select="$countries" mode="parcourt">
                        <xsl:with-param name="dist" select="'inconnu'"/>
                    </xsl:apply-templates>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:element>
    </xsl:template>

    <!-- Template de création des pays dans le parcours des fleuves   -->
    <xsl:template match="country" mode="parcourt">
        <xsl:param name="dist"/>
        <xsl:element name="parcourt">
            <xsl:attribute name="id-pays">
                <xsl:value-of select="@car_code"/>
            </xsl:attribute>
            <xsl:attribute name="distance">
                <xsl:value-of select="$dist"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>

<!--    Template de création des espace maritime-->
    <xsl:template match="sea">
        <xsl:param name="t"/>
        <xsl:element name="espace-maritime">
            <xsl:attribute name="id-e">
                <xsl:value-of select="@id"/>
            </xsl:attribute>
            <xsl:attribute name="nom-e">
                <xsl:value-of select="name"/>
            </xsl:attribute>
            <xsl:attribute name="type">
                <xsl:value-of select="$t"/>
            </xsl:attribute>
            <xsl:apply-templates select="id(./@country)" mode="cotoie"/>
        </xsl:element>
    </xsl:template>

<!--    Template de creation des pays qui cotoie une mer-->
    <xsl:template match="country" mode="cotoie">
        <xsl:element name="cotoie">
            <xsl:attribute name="id-p">
                <xsl:value-of select="@car_code"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>
</xsl:stylesheet>
