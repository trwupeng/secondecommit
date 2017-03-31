package com.kuaikuaidai.kkdaireport.activity;


import android.content.Intent;
import android.os.Bundle;
import android.text.TextUtils;
import android.widget.Button;
import android.widget.EditText;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.base.BaseActivity;
import com.kuaikuaidai.kkdaireport.comm.CallbackInterface;
import com.kuaikuaidai.kkdaireport.comm.CommConstant;
import com.kuaikuaidai.kkdaireport.comm.CommSender;
import com.kuaikuaidai.kkdaireport.parse.MenuParse;
import com.kuaikuaidai.kkdaireport.util.SpUtil;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;

/**
 * 登录
 */
public class LoginActivity extends BaseActivity {

    @BindView(R.id.et_account)
    EditText etAccount;
    @BindView(R.id.et_pwd)
    EditText etPwd;
    @BindView(R.id.bt_login)
    Button btLogin;

    private String acount = null;
    private String pwd = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        ButterKnife.bind(this);
    }

    @OnClick(R.id.bt_login)
    public void onClick() {
        acount = etAccount.getText().toString().trim();
        if (TextUtils.isEmpty(acount)) {
            showToastShort(R.string.name_null);
            return;
        }
        pwd = etPwd.getText().toString().trim();
        if (TextUtils.isEmpty(pwd)) {
            showToastShort(R.string.pwd_null);
            return;
        }
        login();
    }

    private void login() {
        CommSender.login(acount, pwd, this, mContext);
    }

    @Override
    public void onCallback(long code, String msg, Exception e, String api, String useData) {
        if (code == CommConstant.CODE_SUCCESS) {
            CommSender.getMenu(new CallbackInterface() {
                @Override
                public void onCallback(long code, String msg, Exception e, String api, String useData) {
                    if (MenuParse.getInstance().getMenu() != null) {
                        SpUtil.saveObject(mContext, "menu", MenuParse.getInstance().getMenu());
                    }
                    startActivity(new Intent(mContext, MenuActivity.class));
                    finish();
                }
            }, mContext);
        }
    }
}
