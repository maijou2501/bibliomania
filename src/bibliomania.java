import com.docomostar.*;
import com.docomostar.ui.*;
import com.docomostar.util.*;
import com.docomostar.system.*;
import com.docomostar.opt.ui.TouchDevice;
import utility.*;

public class bibliomania extends StarApplication {
	//flag 0=>author, 1=>title, 2=>publisherName, list
	int flag =0;

    public void started(int launchType) {
    	//TouchDevece on
		TouchDevice.setEnabled(true);
     	Display.setCurrent((Frame)new TouchCanvas());
    }

    class TouchCanvas extends Canvas implements TimerListener{

    	//for serch
    	String inputtext;

    	//for barcode input
    	BarcodeRead barcodeRead=new BarcodeRead();

    	//register book number
    	int displayMax = 2000;
    	String[] display = new String[displayMax];

    	//for touch panel
    	int tx = 0;
		int ty = 0;
		int swidth = Display.getWidth();
		int sheight = Display.getHeight();

		//touch orbit length
		static final int MAX_POINT=1000;
		int x[]=new int[MAX_POINT];
		int y[]=new int[MAX_POINT];
		int count;
		boolean tch;

		//first size magnify
		double mag=1.0;
		int magi =0;

		//original image & graphics
    	private Image virtualCanvas;
		private Graphics virtualGraphics;

		//display image & graphics
		public Image TCanvsa ;
		public Graphics TGraphics;

		public TouchCanvas(){
			setSoftLabel(SOFT_KEY_1, "Read");
	        setSoftLabel(SOFT_KEY_2, "Shelving");
	        setSoftLabel(SOFT_KEY_3, "Serch");
	        setSoftLabel(SOFT_KEY_4, "List");

	        //original image size
			virtualCanvas = Image.createImage(getHeight()*2, getWidth());
			virtualGraphics = virtualCanvas.getGraphics();

			//display startup
			for(int h = 0;h<display.length;h++) display[h] = "";

			//touch startup
			tch=false;
			count=0;

			//touch orbit
			Timer tm = new Timer();
			tm.setTime(100);
			tm.setRepeat(true);
			tm.setListener((TimerListener)this);
			tm.start();

			//in case of no textfile, make file
			//exit file, skip this step and read bookcp_a.txt
			SDcard.make("BOOKCP.TXT", "\nPlease push SoftButton shelving, or Read\n");
			SDcard.make("BOOKCP_T.TXT", "\nPlease push SoftButton shelving, or Read\n");
			SDcard.make("BOOKCP_A.TXT", "\nPlease push SoftButton shelving, or Read\n");
			SDcard.make("BOOKCP_P.TXT", "\nPlease push SoftButton shelving, or Read\n");

			String textf = SDcard.road("BOOKCP_A.TXT");
			int num=0 ;
			for(int i=0;; i++){
				num =textf.indexOf("\n");
				if (num == -1) break;
				display[i] = textf.substring(0,num);
				textf = textf.substring(num+1);
			}
		}

		public void paint(Graphics g){

			//clear original
			virtualGraphics.lock();
			virtualGraphics.clearRect(0, 0, getHeight()*2, getWidth());
			//graph index colored
			if(tx==0){
				virtualGraphics.setColor(Graphics.getColorOfName(Graphics.GRAY));
				virtualGraphics.fillRect(0, 0,getHeight()*2, 30);
			}
			//display select sort theme
			if(tx==0){
				switch(flag){
					case 0:
						virtualGraphics.setColor(Graphics.getColorOfName(Graphics.YELLOW));
						virtualGraphics.fillRect(0, 0, 400, 30);
						break;
					case 1:
						virtualGraphics.setColor(Graphics.getColorOfName(Graphics.YELLOW));
						virtualGraphics.fillRect(400, 0, 350, 30);
						break;
					case 2:
						virtualGraphics.setColor(Graphics.getColorOfName(Graphics.YELLOW));
						virtualGraphics.fillRect(750, 0, 200, 30);
						break;
				}
			}
			//display author tab title tab publicheName
			//only index, special
			//draw underline
			virtualGraphics.setColor(Graphics.getColorOfName(Graphics.BLACK));
			for(int i=tx;i<tx+17;i++){
				int itemp;
				String Stemp[]={"","","",""};
				String ttemp = display[i];
				for(int j=0;;j++){
					itemp =ttemp.indexOf("\t");
					if (itemp == -1) break;
					Stemp[j] = ttemp.substring(0,itemp);
					ttemp = ttemp.substring(itemp+1);
				}
				virtualGraphics.drawString(Stemp[0], 0, (i+1-tx)*30);

				if(i-tx!=0)virtualGraphics.clearRect(370, (i-tx)*30, 380, 30);
				virtualGraphics.drawString(Stemp[1], 400, (i+1-tx)*30);

				if(i-tx!=0)virtualGraphics.clearRect(720, (i-tx)*30, 250, 30);
				virtualGraphics.drawString(Stemp[2], 750, (i+1-tx)*30);

				virtualGraphics.drawLine(0,30*(i-tx),getHeight()+100,30*(i-tx));
			}
			virtualGraphics.unlock(true);


			//display setting
			g.lock();
			g.clearRect(0, 0, Display.getWidth(), Display.getHeight());
			//for use display widely
			g.setFlipMode(Graphics.FLIP_ROTATE_RIGHT);
			//for display scale change
			int sw=Display.getWidth();
	        int sh=Display.getHeight();
	        double dwd = mag * (double)(sw);
	        double dhd = mag * (double)(sh);
	        int dw = (int)(dwd);
	        int dh = (int)(dhd);
	        g.drawScaledImage(virtualCanvas,magi,0,dh,dw,ty,0,sh,sw);
			g.unlock(true);

		}

		public void timerExpired(Timer source) {
	        if (tch){
	             x[count]=TouchDevice.getX();
	            y[count]=TouchDevice.getY();
	            count=Math.min(count+1,MAX_POINT-1);

	            int xdis = x[count]-x[0];
	            tx = tx + (xdis/30);
				if(tx<0) tx=0;
				if(tx>displayMax-20) tx =displayMax-20;

	            int ydis = y[count]-y[0];
				ty = ty - ydis;
				if(ty<0) ty=0;
				if(ty>600) ty = 600;

				repaint();
	        }
		}

		public void processEvent(int type, int param){
			//touch panel event
			if (type==Display.TOUCH_PRESSED_EVENT){
			}

			if (type==Display.TOUCH_MOVEDSTART_EVENT){
				count=0;
				tch=true;
			}

			if (type==Display.TOUCH_MOVEDEND_EVENT){
				tch=false;
			}

			if (type==Display.TOUCH_RELEASED_EVENT){
			}


			if (type==Display.TOUCH_PINCHCLOSE_EVENT){
				mag=mag-0.25;
				if (mag==0.75){
					mag =1.00;
				}
				else{
				magi += 120;
				}
				repaint();
			}

			if (type==Display.TOUCH_PINCHOPEN_EVENT){
				mag=mag+0.25;
				magi -= 120;
				repaint();
			}

			//soft panel & key event
			if (type == Display.KEY_RELEASED_EVENT) {
				if (param == Display.KEY_UP) {
					mag=mag-0.25;
					if (mag==0.75){
						mag =1.00;
					}
					else{
					magi += 120;
					}
					repaint();
				}
				if (param == Display.KEY_DOWN) {
					mag=mag+0.25;
					magi -= 120;
					repaint();
				}

				if (param == Display.KEY_LEFT) {
					tx = tx + 5;
					if(tx>displayMax-20) tx =displayMax-20;
					repaint();
				}
				if (param == Display.KEY_RIGHT) {
					tx = tx - 5;
					if(tx<0) tx=0;
					repaint();
				}

				//manual input bookdata
				if(param ==Display.KEY_1){
					String args[]=new String[1];
					args[0]=StarApplicationManager.getSourceURL()+" /bibliomania_ip.php"
//					Launcher.launch(Launcher.LAUNCH_BROWSER_SUSPEND,args);
					Launcher.launch(Launcher.LAUNCH_BROWSER,args);
				}

				//for test simplify update
				if(param ==Display.KEY_2){
					StarApplicationManager sam;
					StarApplication sa = StarApplication.getThisStarApplication();
					sam =(sa.getStarApplicationManager());
					sam.upgrade();
				}

				//connecting web, and save text
				if (param == Display.KEY_SOFT1) {

			    	busing bu = new busing(getGraphics(),"now connecting",20,Display.getHeight()/2);
					bu.kick();
					SDcard.write("BOOKCP.TXT", GetTextFromNet.httpRead("BOOK.TXT"));
					SDcard.write("BOOKCP_T.TXT", GetTextFromNet.httpRead("BOOK_T.TXT"));
					SDcard.write("BOOKCP_A.TXT", GetTextFromNet.httpRead("BOOK_A.TXT"));
					SDcard.write("BOOKCP_P.TXT", GetTextFromNet.httpRead("BOOK_P.TXT"));
					bu.exit();
					repaint();
				}

				//barcodereader
				if (param == Display.KEY_SOFT2) {
					String textbr = barcodeRead.read();
					busing bu = new busing(getGraphics(),"now connecting",20,Display.getHeight()/2);
					bu.kick();
					SendBarcode.httpWrite("bibliomania_bc.php","barcode="+textbr);
					bu.exit();

					//for simple displya
					String textcp = new String(GetTextFromNet.httpRead("BOOK_CP.TXT"));
					Dialog dlg=new Dialog(Dialog.DIALOG_INFO,"Shelving");
					dlg.setText(textcp);
					dlg.show();
				}

				//serch
				if (param == Display.KEY_SOFT3) {
					imeOn(inputtext,DISPLAY_ANY,KANA);
				}

				//change sort list
				if (param == Display.KEY_SOFT4) {
					flag = flag + 1;
					if(flag == 3) flag =0;
					switch(flag){
						case 0:
							String text = SDcard.road("BOOKCP_A.TXT");
							int idx;
							for(int i=0;  ; i++){
								idx =text.indexOf("\n");
								if (idx == -1) break;
								display[i] = text.substring(0,idx);
								text = text.substring(idx+1);
					        }
							tx = 0;
							ty = 0;
							repaint();
							break;

						case 1:
							String text2 = SDcard.road("BOOKCP_T.TXT");
							int idx2;
							for(int i=0;;i++){
								idx2 =text2.indexOf("\n");
								if (idx2 == -1) break;
								display[i] = text2.substring(0,idx2);
								text2 = text2.substring(idx2+1);
					        }
							tx = 0;
							ty = 0;
							repaint();
							break;

						case 2:
							String text3 = SDcard.road("BOOKCP_P.TXT");
							int idx3;
							for(int i=0;;i++){
								idx3 =text3.indexOf("\n");
								if (idx3 == -1) break;
								display[i] = text3.substring(0,idx3);
								text3 = text3.substring(idx3+1);
					        }
							tx = 0;
							ty = 0;
							repaint();
							break;
					}
				}
			}
    	}

		// end input text for serch, call this method
	    // IME_COMMITTED=0- complete input text
	    //IME_CANCELED - cancel input text
	    public void processIMEEvent(int type, String text){
	        if (type == 0) {
	        	inputtext=text;
	        	int sum=1;
				for(int i=1;i<display.length;i++){
					int num =display[i].indexOf(inputtext);
					if (num != -1 ){
						display[sum] = display[i];
						sum ++;
					}
				}
				if (sum != 1){
					for(;sum<display.length;sum++)	display[sum] = "";
				}
	        }
	        tx = 0;
			ty = 0;
	        repaint();
	    }
	}
}
