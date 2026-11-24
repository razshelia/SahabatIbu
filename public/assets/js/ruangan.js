// ruangan
const HH=6.4;box(21,.3,19.5,M(0x6f7a72,.35,.1),0,-.15,4.75);box(90,.3,70,M(0x58656e,.9),0,-.2,50);
[3,8.5].forEach(z=>box(21,.02,.14,M(0xf3c64f,.5),0,.01,z));
const ceil=box(21,.3,19.5,M(0x8d928b,.9),0,HH+.15,4.75);ceil.castShadow=false;
box(21,HH,.3,M(0xcfc9b8,.9),0,HH/2,-4.5);
for(let i=-10;i<=10;i+=1.5)box(.04,HH,.04,M(0xb5af9e,1),i,HH/2,-4.32);
box(.3,HH,19.5,M(0xc3bdac,.9),-10.5,HH/2,4.75);box(.3,HH,19.5,M(0xc3bdac,.9),10.5,HH/2,4.75);
for(let i=-1;i<=1;i++){const l=box(3,.15,.6,M(0xffffff,.2),i*6,HH-.5,1);l.material.emissive=new THREE.Color(0xfff4d0);l.material.emissiveIntensity=.8}
const sign=screen(5,.85,0,HH-1,-4.3);sign.paint((g,W,H)=>{bg(g,W,H,'#2f7d5b');tx(g,'BANK SAMPAH SAHABAT IBU',W/2,H*.5,31,'#fff','center');tx(g,'Perum. Taman Gading, Jember',W/2,H*.82,20,'#cfeedd','center','600')});
const cream=M(0xf1ead8,.5);
if(window.dekor)dekor();
