<?php


namespace FuelSdk;


abstract class WebService
{
    // CRUD users
    const PATH_USER_GET = "/usuarios/item";
    const PATH_USER_SEARCH ="/usuarios/search";
    const PATH_USER_LIST = "/usuarios/list";
    const PATH_USER_CREATE = "/usuarios/create";
    const PATH_USER_UDPATE = "/usuarios/update";
    const PATH_USER_DELETE = "/usuarios/delete?id=4";

    //Login services
    const PATH_AUTH_LOGIN = "/auth/login/user";
    const PATH_AUTH_REFRESH = "auth/token/refresh";

    //CRUD concesionarios
    const PATH_CONCESIONARIO_GET = "/concesionarios/item";
    const PATH_CONCESIONARIO_LIST = "/concesionarios/list";
    const PATH_CONCESIONARIO_CREATE = "/concesionarios/create";
    const PATH_CONCESIONARIO_UPDATE = "/concesionarios/udpate";
    const PATH_CONCESIONARIO_DELETE = "/concesionarios/delete";

    //CRUD clientes
    const PATH_CLIENTE_LIST = "/clientes/list";
}