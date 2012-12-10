package utility;

import com.docomostar.fs.*;
import com.docomostar.device.*;
import com.docomostar.io.*;
import com.docomostar.ui.Dialog;

public class SDcard {

	public static void make(String fileName, String text){

		try{
			StorageDevice sd=StorageDevice.getInstance("/ext0");

			StarAccessToken at = StarStorageService.getAccessToken(0,
			StarStorageService.SHARE_APPLICATION);

			Folder folder=sd.getFolder(at);
			File   file=folder.createFile(fileName);
			FileEntity entWr=file.open(File.MODE_WRITE_ONLY);
			FileDataOutput out=entWr.openDataOutput();
			out.writeString(text);
//			out.writeUTF(text);
			out.flush();
			out.close();
			entWr.close();

		}catch(Exception e){
			System.out.println("Err="+e);
		}
	}

	public static void write(String fileName, byte[] byteAry){

		try{
			StorageDevice sd=StorageDevice.getInstance("/ext0");


			StarAccessToken at = StarStorageService.getAccessToken(0,
			StarStorageService.SHARE_APPLICATION);

			Folder folder=sd.getFolder(at);
			File   file=folder.getFile(fileName);
			FileEntity entWr=file.open(File.MODE_WRITE_ONLY);
			FileDataOutput out=entWr.openDataOutput();

			String write = new String(byteAry);
			out.writeUTF(write);

			out.flush();
			out.close();
			entWr.close();

		}catch(Exception e){
			System.out.println("Err="+e);
			sorry();
		}
	}

	public static void add(String fileName, String text){
		String res ="";

		try{
			StorageDevice sd=StorageDevice.getInstance("/ext0");


			StarAccessToken at = StarStorageService.getAccessToken(0,
			StarStorageService.SHARE_APPLICATION);

			Folder folder=sd.getFolder(at);
			File   file=folder.getFile(fileName);
			FileEntity entRW=file.open(File.MODE_READ_WRITE);

			FileDataInput in=entRW.openDataInput();
			res=in.readString();
			in.close();

			FileDataOutput out=entRW.openDataOutput();
			text = res + text;
			out.writeString(text);
			out.flush();
			out.close();
			entRW.close();

		}catch(Exception e){
			System.out.println("Err="+e);
			sorry();
		}
	}

	public static String road(String fileName){
		String res = "";

		try{
			StorageDevice sd=StorageDevice.getInstance("/ext0");


			StarAccessToken at = StarStorageService.getAccessToken(0,
			StarStorageService.SHARE_APPLICATION);

			Folder folder=sd.getFolder(at);
			File   file=folder.getFile(fileName);
			FileEntity entRd=file.open(File.MODE_READ_ONLY);
			FileDataInput in=entRd.openDataInput();

			res=in.readUTF();
			in.close();
			entRd.close();

			return res;

		}catch(Exception e){
			System.out.println("Err="+e);
			sorry();
		}
		return null;

	}

	public static byte[] roadByte(String fileName){

		try{
			StorageDevice sd=StorageDevice.getInstance("/ext0");


			StarAccessToken at = StarStorageService.getAccessToken(0,
			StarStorageService.SHARE_APPLICATION);

			Folder folder=sd.getFolder(at);
			File   file=folder.getFile(fileName);
			FileEntity entRd=file.open(File.MODE_READ_ONLY);
			FileDataInput in=entRd.openDataInput();

			int l=(int)file.getLength();
			byte byteAry[]=new byte[l];


			for(int i=0;i<l;i++){
				byteAry[i]=(byte)in.readByte();
			}

			in.close();
			entRd.close();

			return byteAry;

		}catch(Exception e){
			System.out.println("Err="+e);
			sorry();
		}
		return null;
	}

	public static void delete(String fileName){

		try{
			StorageDevice sd=StorageDevice.getInstance("/ext0");

			StarAccessToken at = StarStorageService.getAccessToken(0,
			StarStorageService.SHARE_APPLICATION);

			Folder folder=sd.getFolder(at);
			File   file=folder.getFile(fileName);
			file.delete();
		}catch(Exception e){
			System.out.println("Err="+e);
			sorry();
		}
	}

	public static void sorry(){
		Dialog dlg=new Dialog(Dialog.DIALOG_INFO,"sorry");
		dlg.setText("We can't operate\n a SDcard.");
		dlg.show();
	}

	public static void success(){
		Dialog dlg=new Dialog(Dialog.DIALOG_INFO,"success");
		dlg.setText("We can operate\n a SDcard.");
		dlg.show();

	}
}