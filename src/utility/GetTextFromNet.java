package utility;

import com.docomostar.*;
import com.docomostar.ui.*;
import com.docomostar.io.*;
import java.io.*;
import javax.microedition.io.*;

public class GetTextFromNet {

	public static byte[] httpRead(String fileName){

		try{
			String url=StarApplicationManager.getSourceURL();

			HttpConnection htpc=(HttpConnection)Connector.open(url+fileName,Connector.READ,true);
			htpc.setRequestMethod(HttpConnection.GET);
			htpc.connect();

			int l=(int)htpc.getLength();

			byte byteAry[]=new byte[l];

			InputStream in=htpc.openInputStream();


			for(int i=0;i<l;i++){
				byteAry[i]=(byte)in.read();
			}

			in.close();

			htpc.close();

			return byteAry;
		}catch(Exception e){
			System.out.println("Err="+e);
			sorry();
		}
		return null;
	}

	public static void sorry(){
		Dialog dlg=new Dialog(Dialog.DIALOG_INFO,"sorry");
		dlg.setText("We can't get\n a data from web.");
		dlg.show();
	}

	public static void success(){
		Dialog dlg=new Dialog(Dialog.DIALOG_INFO,"success");
		dlg.setText("We can get\n a data.");
		dlg.show();
	}
}