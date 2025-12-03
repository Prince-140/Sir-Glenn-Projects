import express from 'express'
import validationRouter from './routers/Validation.js'
import Update from './routers/Update.js'
import session from 'express-session'
import {Check} from "./routers/middleware.js"
import  {getPost}  from "./utility/db_con.js";

const app = express()
const con = getPost()



app.use(session({
    
    secret:'yawa ra',
    resave: false,
    saveUninitialized: false,
    name: "bwesit"
}))
app.use(express.urlencoded({extended : true}))
app.use(express.json());

app.set("view engine", "ejs")



////Middle ware
const CheckLogin = (req,res,next) => {

if(req.session.user){

    next()
}else{

    res.redirect('/Login')
} }

///middleware
const CheckNotLogin = (req,res,next) => {

if(!req.session.user){

    next()
}else{

    res.redirect('/index')
} }

app.get("/Login",CheckNotLogin,(req, res) => {
res.render('Login',{error:null})


})



app.get("/",CheckNotLogin,(req, res) => {

res.render("home")

})


app.get("/SignUp",CheckNotLogin,(req, res) => {

res.render("SignUp")

})


app.get("/index",CheckLogin,(req, res) => {

    const house = req.session.user.homeName
    
 


     let sql = "SELECT * FROM appliances ORDER BY id DESC ";
   con.query(sql, function (err, result) {

    if (err) {
        throw err;
        return res.status(500).send('Database error');
    }
    res.render('index',{message: house, action: "list", App_List:result})
 
    
   }

  


    
 
)

})






 app.get('/Logout',(req,res) => {
 
  req.session.destroy()
  res.redirect('/')
})




app.get('/switch',(req,res) => {

  res.render('ChangeStatus',{error:null})

})


app.get('/Hardware',(req,res) => {

         con.connect(function(err) {
  if (err) throw err;
  con.query("SELECT Blub_On FROM bulbstatus", function (err, result, fields) {
    if (err) throw err;
   
res.send(result)
  });
});

})


app.get('/display',(req,res) => {

  

     let sql = "SELECT * FROM appliances ORDER BY id DESC ";
   con.query(sql, function (err, result) {

    if (err) {
        throw err;
        return res.status(500).send('Database error');
    }
    
    res.render('Display',{ action: "list", App_List:result})
    
    {
  




    } } )
})





app.use('/Validation',validationRouter)
app.use('/Update',Update)
app.listen(8000)

