package com.kuaikuaidai.kkdaireport.adapter;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.TextView;

import com.kuaikuaidai.kkdaireport.R;
import com.kuaikuaidai.kkdaireport.bean.Menu;

/**
 * 菜单适配器
 */
public class MenuAdapter extends BaseExpandableListAdapter {
    private Context mContext;
    private Menu menu;
    private LayoutInflater mInfalter;

    public MenuAdapter(Context context, Menu list) {
        mContext = context;
        mInfalter = ((Activity) mContext).getLayoutInflater();
        menu = list;
    }

    @Override
    public Object getChild(int groupPosition, int childPosition) {
        return menu.getChildren().get(groupPosition).getChildren().get(childPosition);
    }

    @Override
    public long getChildId(int groupPosition, int childPosition) {
        return childPosition;
    }

    @Override
    public View getChildView(int groupPosition, int childPosition,
                             boolean isLastChild, View convertView, ViewGroup parent) {
        ChildViewHolder childViewHolder;
        if (convertView == null) {
            convertView = mInfalter.inflate(R.layout.item_menu_child, parent, false);
            childViewHolder = new ChildViewHolder();
            childViewHolder.child = (TextView) convertView.findViewById(R.id.tv_menu_child);
            convertView.setTag(childViewHolder);
        } else {
            childViewHolder = (ChildViewHolder) convertView.getTag();
        }
        childViewHolder.child.setText(menu.getChildren().get(groupPosition).getChildren().get(childPosition).getCapt());
        return convertView;
    }

    @Override
    public int getChildrenCount(int groupPosition) {
        return menu.getChildren().get(groupPosition).getChildren().size();
    }

    @Override
    public Object getGroup(int groupPosition) {
        return getGroup(groupPosition);
    }

    @Override
    public int getGroupCount() {
        return menu.getChildren().size();
    }

    @Override
    public long getGroupId(int groupPosition) {
        return groupPosition;
    }

    @Override
    public View getGroupView(int groupPosition, boolean isExpanded,
                             View convertView, ViewGroup parent) {
        GroupViewHolder groupViewHolder;
        if (convertView == null) {
            convertView = mInfalter.inflate(R.layout.item_menu_group, parent, false);
            groupViewHolder = new GroupViewHolder();
            groupViewHolder.group = (TextView) convertView.findViewById(R.id.tv_menu_group);
            convertView.setTag(groupViewHolder);
        } else {
            groupViewHolder = (GroupViewHolder) convertView.getTag();
        }
        String name = menu.getChildren().get(groupPosition).getCapt();
        groupViewHolder.group.setText(name);
        return convertView;
    }

    @Override
    public boolean hasStableIds() {
        return false;
    }

    @Override
    public boolean isChildSelectable(int groupPosition, int childPosition) {
        return true;
    }

    static class GroupViewHolder {
        TextView group;
    }

    static class ChildViewHolder {
        TextView child;
    }

}
