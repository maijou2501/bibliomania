package utility;

import com.docomostar.device.*;
import com.docomostar.system.*;

public class BarcodeRead {
	String res="";

	public BarcodeRead(){
		CodeReader codeReader=CodeReader.getCodeReader(0);
		codeReader.setCode(CodeReader.CODE_JAN13);
	}

	public String read(){
		CodeReader codeReader=CodeReader.getCodeReader(0);
		codeReader.setCode(CodeReader.CODE_JAN13);
		try{
			codeReader.read();
		}catch(InterruptedOperationException e){
			return res="";
		}
		res=codeReader.getString();
		return res;
	}
}