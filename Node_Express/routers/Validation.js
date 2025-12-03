import express from 'express'
import  {getPost}  from "../utility/db_con.js";


const router = express.Router()
const con = getPost()


//Username variable

let homename 
let firstname 
let lastname  
let username  
let password  
let status = false


//Login Validation
router.post("/",(req, res) =>
{

username  = req.body.username
password  = req.body.password

con.connect(function(err) {
  if (err) throw err;
  console.log("Connected!");

  let sql = "SELECT id,homeName FROM users WHERE `userName` = ? AND `passWord` =?";
    con.query(sql, [username,password], (err, result) => {
    if (err) {

    throw err;
    
    }else if (result.length > 0)
    {

   const atay = result[0];

   req.session.user = {id:atay.id, homeName:atay.homeName }
   res.redirect('/index')

   
    }else{
           res.render('Login',{error:'Invalid Username or Password'})
    }

  
  });
});


})



//Create User, database connection
router.post("/Create",(req, res) =>
{
 
homename = req.body.housename
firstname = req.body.firstname
lastname  = req.body.lastname
username  = req.body.username
password  = req.body.password
 


 con.connect(function(err) {
  if (err) throw err;
  console.log("Connected!");
  let sql = "INSERT INTO users (homename, firstName,lastName,userName,passWord) VALUES ('"+homename+"','"+firstname+"','"+lastname+"','"+username+"','"+password+"')";
    con.query(sql, function (err, result) {
    if (err) {
        throw err;
    }else{
    console.log("Added successfully");
    res.redirect("/Login")

}
  });
});



})


 
router.get('/Logout',(req,res,) => {
 
  req.session.destroy()
  res.redirect('/')
})





export default router;