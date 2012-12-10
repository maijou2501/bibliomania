package utility;
import com.docomostar.ui.*;
import com.docomostar.media.*;

public class busing implements Runnable{

	public busing(Graphics g,String message,int xpos,int ypos){
		this.x=xpos;
		this.y=ypos;
		this.g=g;
		this.mess=message;
	}

	private void repaint(){
		g.lock();
		MediaImage mImage = MediaManager.getImage("resource:///book0"+i+".gif");
        try{
            mImage.use();
        }catch(Exception e){
        }
        Image image=mImage.getImage();
        int sw=image.getWidth();//�\�[�X���̕��̓C���[�W���Ɠ���102
        int sh=image.getHeight();//�\�[�X���̍����̓C���[�W�̍���70
        int dw=(int)(sw*3);//�\���̕��́A�\�[�X�̕��~�{��
        int dh=(int)(sh*3);//�\���̍����́A�\�[�X�̍����~�{��
        g.clearRect(0, y-100, 500,210);
        g.drawScaledImage(image,100,y-100,dw,dh,0,0,sw,sh);//�C���[�W�̊g��k���\��

		Font fmax = getMaxFont();
		g.setFont(fmax);
		if(blink)g.setColor(Graphics.getColorOfName(Graphics.GREEN));
		else g.setColor(Graphics.getColorOfName(Graphics.RED));
		g.drawString(mess, x,y);
		blink=!blink;
		i =i + 1;
		if(i==6)i=1;
		g.unlock(true);
	}

	Font getMaxFont(){
        int fontSizes[]=Font.getSupportedFontSizes();
        return Font.getFont(Font.FACE_SYSTEM,fontSizes[fontSizes.length-1]);
    }

	private Thread th=new Thread(this);
	private int x,y,i=1;
	private String mess;
	private Graphics g;
	private boolean blink=false;
	private boolean alive=true;
	public void exit(){
		alive=false;
	}

	public void kick(){
		th.start();
	}

	public void run() {
		try{
			while(alive){
				repaint();
				Thread.sleep(700);
			}
		}
		catch (Exception e){
		}
	}
}