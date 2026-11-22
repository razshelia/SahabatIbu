// Latar garasi 3D untuk halaman non-garasi: adegan sama, tanpa interaksi.
const VW={home:{p:[0,3.4,13.2],t:[0,2,-1]},timbang:{p:[8.5,3,10],t:[4.2,1.6,4.8]},struk:{p:[3,3,8],t:[-1.2,1.4,1.8]},capit:{p:[-.8,3.4,6.5],t:[-4.2,2.2,-2.2]},rak:{p:[-.5,3.6,15],t:[-6.2,2.2,7.8]},luar:{p:[0,5,32],t:[0,3,14.3],pintu:1},scan:{p:[12,3.4,16],t:[7.2,1.8,9.6]},riwayat:{p:[.4,3.4,7],t:[4.6,1.6,-.4]}};
const K=VW[document.body.dataset.view]||VW.luar,RM=matchMedia('(prefers-reduced-motion:reduce)').matches;
HD.forEach(x=>x.visible=false);LB.forEach(x=>x.sp.visible=false);RG.forEach(x=>x.m.visible=false);sw.visible=K===VW.luar;
function size(){R.setSize(innerWidth,innerHeight);cam.aspect=innerWidth/innerHeight;cam.updateProjectionMatrix()}
addEventListener('resize',size);size();rcptDraw(null);redraw();
const T0=performance.now();
(function loop(){requestAnimationFrame(loop);const t=(performance.now()-T0)/1e3,o=RM||!K.pintu?1:sm(cl(t/2.4));
 dr.scale.y=K.pintu?1-.92*o:.08;dl.intensity=K.pintu?o*1.3:1.3;
 const w=RM?0:1;cam.position.set(K.p[0]+Math.sin(t/3)*.35*w,K.p[1]+Math.cos(t/4)*.12*w,K.p[2]);cam.lookAt(K.t[0],K.t[1],K.t[2]);R.render(sc,cam)})();
