export default (to,from,next)=>{
    if(to.meta.authenticated&&sessionStorage.getItem('auth')){
        next();
    }

    if(!to.meta.authenticated)
         next();

    return next('/login');
};