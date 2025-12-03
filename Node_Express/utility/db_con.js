import mysql from 'mysql2';

const HostValue = "localhost";
const Username = "Prince";
const Password = "";
const Db_name  = "sensors"
const PORT = 3307;


//Make A connection.
const con = mysql.createConnection({
  host: HostValue ,
  user: Username,         
  password: Password,            
  database: Db_name,     
  port: PORT              
});

const testcon = mysql.createPool({
  host: HostValue,
  user: Username,
  password: Password,   
  database:Db_name,
  port: PORT,        
  waitForConnections: true,
  connectionLimit: 5,
  maxIdle: 5, // max idle connections, the default value is the same as `connectionLimit`
  idleTimeout: 60000, // idle connections timeout, in milliseconds, the default value 60000
  queueLimit: 0,
  enableKeepAlive: true,
  keepAliveInitialDelay: 0,
});



//Export the connection.
const getPost = () => con;
export {getPost};

 
