import express from 'express'
import  {getPost}  from "../utility/db_con.js";


const router = express.Router()
const con = getPost()


 //Create Appliances
router.post('/create',(req,res,) => {
 
const App_name = req.body.App_name
const App_type = req.body.App_type
const App_Power = req.body.App_power

 con.connect(function(err) {
  if (err) throw err;
  console.log("Connected!");
  let sql = "INSERT INTO appliances (Name,Type,WattUsage) VALUES ('"+App_name+"','"+App_type+"','"+App_Power+"')";
    con.query(sql, function (err, result) {
    if (err) {
        throw err;
    }else{
     res.redirect('/index')
}
  });  
});

})

 //Remove Appliances
router.post('/remove',(req,res,) => {
 
const Id = req.body.remove


 con.connect(function(err) {
  if (err) throw err;
  console.log("Connected!");
  let sql = "DELETE FROM appliances WHERE Id ='"+Id+"'";
    con.query(sql, function (err, result) {
    if (err) {
        throw err;
    }else{

      console.log("Succkkness")
     res.redirect('/index')
}
  });  
}); 

}) 



 
 


 
 
 

export default router;