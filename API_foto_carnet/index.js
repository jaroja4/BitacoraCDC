var express = require("express");
const sql = require('mssql')


const config = {
    user: 'dcuser',
    password: 'yb0Y2VR8cVyii4',
    server: 'SABSQL10\\VCONTINUUM',
    database: 'ContinuumDB',
    domain: 'ice',
    options: {
        enableArithAbort: true // required, otherwise deprecation warning
    }
}

console.log("start");

sql.connect(config)
  .then((conn) => {
    console.log('MSSQL: connected');
    conn.request().query(`SELECT Nombre, Personnel_SocSecNo AS Cedula, (select FOTO as '*' for xml path('')) as Foto FROM  [ContinuumDB].[dbo].[v_RH] WHERE Personnel_SocSecNo = '114140310'  AND TotalSize is not null`)
      .then(data =>
        console.log(JSON.stringify(data)))
      .then(() => conn.close())
  }).catch(err => { console.log(err) });
