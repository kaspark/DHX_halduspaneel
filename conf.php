<?php

## DateTime zone
date_default_timezone_set('Europe/Tallinn');

## APP CONF
#Kas kasutab kasutajate tuvastamiseks AD või lokaalset
$AD_login = true;
#Kas näitame pikka vea kirjeldust vigade nimekirjas
$showErrorDesc = false;
#Vaikimisi mitut rida vigaseid saatmisi kuvatakse
$errorRowCount = 5;
#Vaikimis mitut rida saadetuid dokumente kuvatakse
$sentRowCount = 10;
#Vaikimis mitut rida vastuvõetud dokumente kuvatakse
$receiveRowCount = 10;
#Kas näitame esindatud asutusi
$showRepInst = true;

## LOCAL LOGIN CONF
$LL_user = 'admin';
$LL_pass = 'pass';

## AD LOGIN CONF
$AD_host = 'admasin.domee.ee';
$AD_domain = 'domeen.ee';
$AD_basedn = 'dc=domeen,dc=ee';
$AD_group = 'DHX_vaatamine';

## DB INFO
$DB_host = 'localhost';
$DB_user = 'admin';
$DB_pass = 'pass';


## ASUTUSTE LIST VASTAVALT BAASI NIMEDELE
## "ASUTUSE_LÜHEND" => ["ASUTUSE_NIMI", "BAASI_NIMI", "FAILIDE_ASUKOHT"]
## ASUTUSE_LÜHEND = muutuja millega aadressi ribal asutusi eristada nt asutus1, DOK1 või systeem111
## ASUTUSE_NIMI = asutuse nimi peab võrduma asutus tabelis oleva nimega
## BAASI_NIMI = asutuse baasi nimi
## FAILIDE_ASUKOHT = asutuse failide asukoht serveris
$inst_list = [
        "dhx01" => ["ASUTUSE NIMI", "dhx_baasi_nimi", "/data/dhx/failide/asukoht"]
        ];