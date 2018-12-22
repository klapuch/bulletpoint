// @flow
import React from 'react';
import classNames from 'classnames';
import styled from 'styled-components';
import type { PostedBulletpointType } from '../theme/bulletpoint/types';

export type FormTypes = 'default' | 'edit' | 'add';
type TargetType = {|
  target: {|
    name: string,
    value: string,
  |},
|};
type ButtonProps = {|
  formType: FormTypes,
  onClick: () => (void),
|};


const SpaceLink = styled.a`
  margin-right: 5px;
`;


const SubmitButton = ({ formType, onClick, children }: { children: string, ...ButtonProps }) => {
  const className = classNames('btn', formType === 'default' ? 'btn-default' : 'btn-success');
  return (
    <SpaceLink className={className} onClick={onClick} role="button">{children}</SpaceLink>
  );
};

const ConfirmButton = ({ formType, onClick }: ButtonProps) => {
  if (formType === 'default') {
    return <SubmitButton formType={formType} onClick={onClick}>Přidat bulletpoint</SubmitButton>;
  } else if (formType === 'add') {
    return <SubmitButton formType={formType} onClick={onClick}>Přidat</SubmitButton>;
  } else if (formType === 'edit') {
    return <SubmitButton formType={formType} onClick={onClick}>Upravit</SubmitButton>;
  }
  return null;
};

const CancelButton = ({ formType, onClick, children }: { children: string, ...ButtonProps }) => {
  if (formType === 'default') {
    return null;
  }
  return (
    <SpaceLink className="btn btn-danger" onClick={onClick} role="button">{children}</SpaceLink>
  );
};


type Props = {|
  +bulletpoint: ?PostedBulletpointType,
  +onSubmit: (PostedBulletpointType) => (void),
  +onAddClick: () => (void),
  +onCancelClick: () => (void),
  +type: FormTypes,
|};
type State = {|
  bulletpoint: PostedBulletpointType,
|};
const initState = {
  bulletpoint: {
    content: '',
    source: {
      link: '',
      type: 'web',
    },
  },
};
export default class extends React.Component<Props, State> {
  state = initState;

  componentWillReceiveProps(nextProps: Props): void {
    if (nextProps.bulletpoint !== null) {
      this.setState({ bulletpoint: nextProps.bulletpoint });
    }
  }

  onChange = ({ target: { name, value } }: TargetType) => {
    let input = null;
    if (name === 'source_link') {
      input = { source: { ...this.state.bulletpoint.source, link: value } };
    } else if (name === 'source_type') {
      input = { source: { type: value, link: '' } };
    } else {
      input = { ...this.state.bulletpoint, [name]: value };
    }
    this.setState(prevState => ({
      bulletpoint: {
        ...prevState.bulletpoint,
        ...input,
      },
    }));
  };

  onSubmit = () => {
    this.props.onAddClick();
    this.props.onSubmit(this.state.bulletpoint);
    this.setState(initState);
  };

  onCancelClick = () => {
    this.props.onCancelClick();
    this.setState(initState);
  };

  render() {
    const { bulletpoint } = this.state;
    return (
      <>
        {this.props.type === 'default' ? null : (
          <form>
            <div className="form-group">
              <label htmlFor="content">Obsah</label>
              <input type="text" className="form-control" id="content" name="content" value={bulletpoint.content} onChange={this.onChange} />
            </div>
            <div className="form-group">
              <label htmlFor="source_type">Typ zdroje</label>
              <select className="form-control" id="source_type" name="source_type" value={bulletpoint.source.type} onChange={this.onChange}>
                <option value="web">Web</option>
                <option value="head">Z vlastní hlavy</option>
              </select>
              {
                bulletpoint.source.type === 'web'
                  ? <>
                    <label htmlFor="source_link">Odkaz na zdroj</label>
                    <input type="text" className="form-control" id="source_link" name="source_link" value={bulletpoint.source.link} onChange={this.onChange} />
                  </>
                  : null
              }
            </div>
          </form>
        )}
        <ConfirmButton onClick={this.onSubmit} formType={this.props.type} />
        <CancelButton onClick={this.onCancelClick} formType={this.props.type}>
          Zrušit
        </CancelButton>
      </>
    );
  }
}
