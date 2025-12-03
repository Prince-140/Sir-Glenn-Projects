
const CheckLogin = (req,res,next) => {

if(req.session.user){

    next()
}else{

    res.redirect('/Login')
}

} 


const Check = () => CheckLogin;
export {Check};