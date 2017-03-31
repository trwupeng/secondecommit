package com.kuaikuaidai.kkdaireport.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.bean.ContractIds;

import java.util.List;

/**
 * 渠道适配器
 */
public class ChannelAdapter extends BaseAdapter {
    private List<ContractIds> mList;
    private Context mContext;
    private LayoutInflater mInflater;
    private ViewHolder mViewHolder;

    public ChannelAdapter(Context context, List<ContractIds> list) {
        this.mContext = context;
        this.mList = list;
        mInflater = LayoutInflater.from(mContext);
    }

    @Override
    public int getCount() {
        return mList.size();
    }

    @Override
    public Object getItem(int position) {
        return mList.get(position);
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        if (convertView == null) {
            mViewHolder = new ViewHolder();
            convertView = mInflater.inflate(android.R.layout.simple_spinner_item, null);
            mViewHolder.name = (TextView) convertView.findViewById(android.R.id.text1);
            convertView.setTag(mViewHolder);
        } else {
            mViewHolder = (ViewHolder) convertView.getTag();
        }
        mViewHolder.setName(mList.get(position));
        return convertView;
    }


    class ViewHolder {
        TextView name;

        public void setName(ContractIds contractIds) {
            name.setText(contractIds.getName());
        }
    }


}
