function dn(){
            var passwed=document.getElementById("passwed").value
            var user = document.getElementById("name").value
            var ing=document.getElementById('ing')
            ing.innerHTML='logining'
            if(user==="150")
            {
                if(passwed=="12345")
                {
                    ing.innerHTML='ok'
                   localStorage.setItem('user',user)
                   location.href="../../index.html"
                }
                else
                {
                    ing.innerHTML='passwed no'    
                }
            }
            else
            {
                ing.innerHTML='user no'  
            }
        }
        function tozuce(){
            location.href="./user/zuce.html"
        }
        function esc(){
           location.href="../../index.html"
        }
        function help(){
            alert("150;12345")
        }