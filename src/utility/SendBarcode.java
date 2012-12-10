package utility;

import com.docomostar.*;
import com.docomostar.ui.*;
import com.docomostar.io.*;
import javax.microedition.io.*;

public class SendBarcode {

	public static void httpWrite(String cgiName,String text){

		try{
			String url=StarApplicationManager.getSourceURL()+cgiName+"?"+text;
			HttpConnection http = (HttpConnection)Connector.open(url,Connector.READ, true);
			http.setRequestMethod(HttpConnection.GET);
			http.connect();
			http.close();

		}catch(Exception e){
			System.out.println("Err="+e);
			sorry();
		}
	}
/*
	public static void httpWrite(String cgiName,String text){

		try{
			String url=StarApplicationManager.getSourceURL()+cgiName;


			 HttpConnection httpSend=(HttpConnection)Connector.open(url,Connector.WRITE,true);


			 httpSend.setRequestMethod(HttpConnection.POST);
			 //
			 httpSend.setRequestProperty("Content-Type","text/plain");

			OutputStream outs=httpSend.openOutputStream();
			byte sendByte[]=text.getBytes();
//			byte sendByte[]=text.readUTF();
			for(int i=0;i<sendByte.length;i++)outs.write(sendByte[i]);
			outs.flush();
			outs.close();

			httpSend.connect();
			httpSend.close();

//			success();

		}catch(Exception e){
			System.out.println("Err="+e);
			sorry();
		}
	}
*/

	public static void sorry(){
		Dialog dlg=new Dialog(Dialog.DIALOG_INFO,"sorry");
		dlg.setText("We can't send\n a data from cell phone.");
		dlg.show();
	}

	public static void success(){
		Dialog dlg=new Dialog(Dialog.DIALOG_INFO,"success");
		dlg.setText("We can send a data.");
		dlg.show();

	}
}