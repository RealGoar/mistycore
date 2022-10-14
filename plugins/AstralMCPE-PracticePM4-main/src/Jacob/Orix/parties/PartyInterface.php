<?php

namespace Jacob\Orix\parties;

interface PartyInterface
{

    function getLeader();
    function getMembers();
    function getMembersCount();
    function getMaxSize();
    function getPremium();

}