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
            location.href="./zuce.html"
        }
        function esc(){
           location.href="../../index.html"
        }
        function help(){
            alert("150;12345")
        }
        function testout() {
            // 前端获取参数值
            const urlParams = new URLSearchParams(window.location.search);
            const ov = urlParams.get('key'); // 得到 "1"
            alert(ov)
        }