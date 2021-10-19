

export const hasRoles=roles=>{
    const ses_roles=JSON.parse(window.atob(window.sessionStorage.getItem('roles')));

    let foundroles=Array();
    roles.forEach(role=> {
            let foundrole = ses_roles.filter(ses_role => ses_role.name == role);
            if(foundrole.length>0)
            foundroles.push(
                foundrole
            )
        }
    );

    return foundroles.length>0;
};
export const usePermission=task=>new Promise((resolve,reject)=>{
    if(hasRoles(['admin'])) {
        resolve({allowed: true});
    }else{
        const ses_profile_permissions=JSON.parse(window.atob(window.sessionStorage.getItem('profile_permissions')));
       for(const key in ses_profile_permissions){
           const permission=ses_profile_permissions[key].filter(permission=>permission.name===task&&permission.allow===1);
           if(permission.length>0)
               resolve({allowed: true});
       }

        reject({allow:false});
    }
});
export const formatPrice=price=>`£${price !== 0 ? price.toFixed(2) : 0}`;

/**
    blanc date format helper
    @param String date_str Date in the format for Y-m-d
    @param String format default DD/MM (ex 27/09), DAY DD/MM (ex MON 27/09)
**/
export const formatDate=(date_str,format)=>{
  
    if(typeof format==="undefined")
        format='DD/MM';
    const date=new Date(date_str);
    let options = {  month: 'numeric', day: 'numeric' };
    if(format==='DAY DD/MM')
        options = { weekday: 'short',  month: 'numeric', day: 'numeric' };
    if(format==='DAYL DD/MM')
    options = { weekday: 'long',  month: 'numeric', day: 'numeric' };    

    const dateTimeFormat = new Intl.DateTimeFormat('en-GB', options);
    return dateTimeFormat.format(date).replace(',','').toUpperCase();
}

