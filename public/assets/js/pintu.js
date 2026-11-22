// PINTU GARASI DEPAN
const dr=new THREE.Group();dr.position.set(0,HH,14.3);sc.add(dr);const NS=9,SH=HH/NS;
for(let i=0;i<NS;i++)box(20.6,SH-.04,.2,M(i%2?0xe9e5da:0xdfdbcf,.6),0,-(i+.5)*SH,0,dr);
box(1.2,.1,.14,M(0x26332d),0,-(HH-.4),.14,dr);
const dsign=screen(10,1.9,0,-HH*.5,.12,dr,0,1024);dsign.paint((g,W,H)=>{bg(g,W,H,'#2f7d5b');tx(g,'BANK SAMPAH SAHABAT IBU',W/2,H*.48,80,'#fff','center');tx(g,'Gulir ke bawah atau klik pintu untuk masuk',W/2,H*.8,44,'#cfeedd','center','700')});
[-10.3,10.3].forEach(x=>box(.4,HH+.4,.5,cream,x,HH/2,14.3));box(21.2,.5,.5,cream,0,HH+.2,14.3);
const dl=new THREE.PointLight(0xfff1cc,0,28);dl.position.set(0,3.5,10);sc.add(dl);
const sw=new THREE.Group();sw.position.set(-6.5,0,15.8);sc.add(sw);box(.16,1.3,.16,M(0x26332d),0,.65,0,sw);box(2.4,1.6,.14,M(0x26332d),0,2.0,0,sw);
screen(2.2,1.4,0,2.0,.08,sw).paint((g,W,H,B)=>{bg(g,W,H);tx(g,'Pintu garasi',24,58,38,'#fff');tx(g,S.door?'Terbuka':'Tertutup',24,128,52,S.door?'#6fe0a2':'#f3c64f');B(24,H-118,W-48,92,'Masuk garasi',()=>goSec('scan'),'g')});
tag(sw,'luar');tag(dr,'luar');
[[pm,'scan'],[tm,'timbang'],[pr,'struk'],[bk,'riwayat'],[rk,'rak'],[cp,'capit']].forEach(a=>tag(a[0],a[1]));

